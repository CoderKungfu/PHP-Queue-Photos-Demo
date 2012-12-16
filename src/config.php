<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
PHPQueue\Base::$queue_path = __DIR__ . '/queues/';
PHPQueue\Base::$worker_path = __DIR__ . '/workers/';

class PhotoConfig
{
    static public $backend_types = array(
              'Beanstalkd' => array(
                    'config' => array(
                            'server' => '127.0.0.1'
                          , 'tube'   => 'queue1'
                     )
                )
            , 'WindowsAzureServiceBus' => array(
                    'config' => array(
                            'connection_string' => ''
                          , 'queue'             => 'noobq'
                     )
                )
        );
}