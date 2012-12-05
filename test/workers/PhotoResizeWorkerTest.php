<?php
class PhotoResizeWorkerTest extends \PHPUnit_Framework_TestCase
{
    private $object;

    public function setUp()
    {
        parent::setUp();
        $this->object = \PHPQueue\Base::getWorker('PhotoResize');
    }

    public function testRunJob()
    {
        $data1 = array(
              'worker' => 'Sample'
            , 'data' => array('var1'=>"Milo")
        );
        $job = new \PHPQueue\Job($data1);
        $this->object->runJob($job);
        $this->assertEquals(
                  array('var1'=>"Milo",'var2'=>"Welcome back!")
                , $this->object->result_data
            );
    }
}
?>