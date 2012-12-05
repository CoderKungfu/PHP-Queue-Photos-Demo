<?php
class MoveFileWorker extends PHPQueue\Worker
{
    public function runJob($jobObject)
    {
        parent::runJob($jobObject);
        $jobData = $jobObject->data;
        $this->result_data = $jobData;
    }
}