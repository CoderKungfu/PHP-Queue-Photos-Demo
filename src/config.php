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
                      , 'queue'             => 'photosqueue'
                )
	        , 'WindowsAzureBlobUploadContainer' => array(
		                'container' => 'photosupload'
	            )
		    , 'WindowsAzureBlobCDNContainer' => array(
						'container' => 'photoscdn'
			    )
        );

    static public function getConfig($type=null)
    {
        $config = isset(self::$backend_types[$type]) ? self::$backend_types[$type] : array();
        $config['backend'] = $type;
        switch($type)
        {
            case 'WindowsAzureServiceBus':
                $config['connection_string'] = getenv('queue_connection_string');
                break;
	        case 'WindowsAzureBlobUploadContainer':
		    case 'WindowsAzureBlobCDNContainer':
	            $config['connection_string'] = getenv('wa_blob_connection_string');
                $config['backend'] = 'WindowsAzureBlob';
		        break;
        }
        return $config;
    }
}