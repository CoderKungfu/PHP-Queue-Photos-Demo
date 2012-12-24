<?php
class UploadCDNWorker extends PHPQueue\Worker
{
    /**
     * @var PHPQueue\Backend\FS
     */
    static private $data_source;

    public function __construct()
    {
        parent::__construct();
	    $upload_target = getenv('cdn_target') ? getenv('cdn_target') : 'WindowsAzureBlobCDNContainer';
	    $upload_options = PhotoConfig::getConfig($upload_target);
        self::$data_source = \PHPQueue\Base::backendFactory($upload_options['backend'], $upload_options);
    }

    /**
     * @param \PHPQueue\Job $jobObject
     */
    public function runJob($jobObject)
    {
        parent::runJob($jobObject);
        $jobData = $jobObject->data;
        if (empty($jobData['uploads']))
        {
            throw new PHPQueue\Exception\Exception('Result files not found.');
        }
        $status = true;
        foreach($jobData['uploads'] as $upload)
        {
            if (is_file($upload['file']))
            {
                self::$data_source->putFile($upload['filename'], $upload['file']);
            }
            else
            {
                $jobData['errors'][] = sprintf('Unable to upload %s', $upload['file']);
                $status = false;
            }
        }
        $jobData['success'] = $status;
        $this->result_data = $jobData;
    }
}