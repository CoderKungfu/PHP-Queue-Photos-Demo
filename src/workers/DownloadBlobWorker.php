<?php
class DownloadBlobWorker extends PHPQueue\Worker
{
    /**
     * @var \PHPQueue\Backend\FS
     */
    static private $data_source;
    private $download_folder;

    public function __construct()
    {
        parent::__construct();
	    $upload_target = getenv('upload_target') ? getenv('upload_target') : 'WindowsAzureBlobUploadContainer';
	    $upload_options = PhotoConfig::getConfig($upload_target);
	    self::$data_source = \PHPQueue\Base::backendFactory($upload_options['backend'], $upload_options);
        $this->download_folder = __DIR__ . '/downloads/';
    }

    /**
     * @param \PHPQueue\Job $jobObject
     */
    public function runJob($jobObject)
    {
        parent::runJob($jobObject);
        $jobData = $jobObject->data;
        if (empty($jobData['blobname']))
        {
            throw new PHPQueue\Exception\Exception('Blob not found.');
        }
        $blob_name = $jobData['blobname'];
        $download_path = $this->download_folder . $blob_name;
        self::$data_source->fetchFile($blob_name, $download_path);
        $jobData['downloaded_file'] = $download_path;
        $this->result_data = $jobData;
    }
}