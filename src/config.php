<?php
require_once dirname(__DIR__) . '/vendor/autoload.php';
PHPQueue\Base::$queue_path = __DIR__ . '/queues/';
PHPQueue\Base::$worker_path = __DIR__ . '/workers/';

class PhotoConfig
{
    static public $backend_types = array(
              'Beanstalkd' => array(
                      'backend'   => 'Beanstalkd'
                    , 'server'    => '127.0.0.1'
                    , 'tube'      => 'queue1'
                )
            , 'WindowsAzureServiceBus' => array(
                      'backend'   => 'WindowsAzureServiceBus'
                    , 'queue'     => 'photosqueue'
                )
	        , 'WindowsAzureBlobUploadContainer' => array(
                      'backend'   => 'WindowsAzureBlob'
		            , 'container' => 'photosupload'
	            )
		    , 'WindowsAzureBlobCDNContainer' => array(
                      'backend'   => 'WindowsAzureBlob'
                    , 'container' => 'photoscdn'
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
	        case 'WindowsAzureBlobUploadContainer':
		    case 'WindowsAzureBlobCDNContainer':
	            $config['connection_string'] = getenv('wa_blob_connection_string');
		        break;
        }
        return $config;
    }
}