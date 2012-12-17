<?php
class PhotosQueue extends PHPQueue\JobQueue
{
    private $dataSource;
    private $queueWorker = array('PhotoResize', 'MoveFile');
    private $resultLog;

    public function __construct()
    {
        $type = getenv('backend_target');
        if (empty($type))
        {
            $type = 'Beanstalkd';
        }
        $config = PhotoConfig::getConfig($type);
        $this->dataSource = \PHPQueue\Base::backendFactory($type, $config);
        $this->resultLog = \PHPQueue\Logger::createLogger(
                              'PhotosLogger'
                            , PHPQueue\Logger::INFO
                            , __DIR__ . '/logs/results.log'
                        );
    }

    public function addJob(array $newJob)
    {
        $formatted_data = array('worker'=>$this->queueWorker, 'data'=>$newJob);
        $this->dataSource->add($formatted_data);
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
}