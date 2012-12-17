<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
PHPQueue\Base::$queue_path = __DIR__ . '/queues/';
PHPQueue\Base::$worker_path = __DIR__ . '/workers/';

class PhotoConfig
{
    static public $backend_types = array(
              'Beanstalkd' => array(
                        'server' => '127.0.0.1'
                      , 'tube'   => 'queue1'
                )
            , 'WindowsAzureServiceBus' => array(
                        'connection_string' => ''
                      , 'queue'             => 'noobq'
                )
        );

    static public function getConfig($type=null)
    {
        $config = isset(self::$backend_types[$type]) ? self::$backend_types[$type] : array();
        switch($type)
        {
            case 'WindowsAzureServiceBus':
                $config['connection_string'] = getenv('queue_connection_string');
                break;
        }
        return $config;
    }
}