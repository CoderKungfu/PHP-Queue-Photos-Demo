<?php
class PhotosQueue extends PHPQueue\JobQueue
{
	/**
	 * @var PHPQueue\Backend\Base
	 */
	private $dataSource;
	/**
	 * @var PHPQueue\Backend\FS
	 */
	private $blobSource;
	private $queueWorker = array('DownloadBlob', 'PhotoResize', 'UploadCDN');
    private $resultLog;

    public function __construct()
    {
        $type = getenv('backend_target') ? getenv('backend_target') : 'WindowsAzureServiceBus';
	    $config = PhotoConfig::getConfig($type);
	    $this->dataSource = \PHPQueue\Base::backendFactory($type, $config);

	    $upload_target = getenv('upload_target') ? getenv('upload_target') : 'WindowsAzureBlobUploadContainer';
	    $upload_options = PhotoConfig::getConfig($upload_target);
	    $this->blobSource = \PHPQueue\Base::backendFactory($upload_options['backend'], $upload_options);

        $this->resultLog = \PHPQueue\Logger::createLogger(
                              'PhotosLogger'
                            , PHPQueue\Logger::INFO
                            , __DIR__ . '/logs/results.log'
                        );
    }

    public function addJob(array $newJob)
    {
	    if (empty($newJob['file']) || !is_file($newJob['file']))
	    {
		    throw new \PHPQueue\Exception\Exception('File not found.');
	    }
	    if (empty($newJob['filename']))
	    {
		    $newJob['filename'] = $newJob['file'];
	    }
	    $newJob['blobname'] = $this->genBlobName($newJob['filename']);
	    $this->blobSource->putFile($newJob['blobname'], $newJob['file']);
	    unset($newJob['file']);

	    $formatted_data = array('worker'=>$this->queueWorker, 'data'=>$newJob);
	    $this->dataSource->add($formatted_data);
	    $this->resultLog->addInfo('Adding new job: ', $newJob);
	    return true;
    }

    public function getJob()
    {
        $data = $this->dataSource->get();
        $nextJob = new \PHPQueue\Job($data, $this->dataSource->last_job_id);
        $this->last_job_id = $this->dataSource->last_job_id;
        return $nextJob;
    }

    public function updateJob($jobId = null, $resultData = null)
    {
	    $this->blobSource->clear($resultData['blobname']);
        $this->resultLog->addInfo('Result: ID='.$jobId, $resultData);
    }

    public function clearJob($jobId = null)
    {
        $this->dataSource->clear($jobId);
    }

    public function releaseJob($jobId = null)
    {
        $this->dataSource->release($jobId);
    }

	private function genBlobName($file_path)
	{
		$blob_key = md5( sprintf('%s-%s', $file_path, time()) );
		$ext = substr($file_path, strrpos($file_path, '.'));
		return sprintf('%s%s', $blob_key, $ext);
	}
}