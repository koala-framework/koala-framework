<?php
/**
 * @group Update_Action
 * @group Update_Action_Rrd
 */
class Vps_Update_Action_Rrd_AddDsTest extends Vps_Update_Action_Rrd_AbstractTest
{
    public function testAddRrd()
    {
        $file = $this->_createTestFile();

        $action = new Vps_Update_Action_Rrd_AddDs(array(
            'file' => $file,
            'name' => 'test3',
            'type' => 'ABSOLUTE',
            'minimalHeartbeat' => 120,
            'min' => 0,
            'max' => 1000,
            'backup'=>false,
            'silent' => true
        ));
        $action->preUpdate();
        $action->update();
        $action->postUpdate();

        $cmd = "rrdtool dump $file > $file.xml";
        $this->_systemCheckRet($cmd);

        $xml = simplexml_load_file($file.'.xml');

        $this->assertEquals(3, count($xml->ds));
        $this->assertEquals('test1', trim($xml->ds[0]->name));
        $this->assertEquals('test2', trim($xml->ds[1]->name));
        $this->assertEquals('test3', trim($xml->ds[2]->name));

        unlink($file);
        unlink($file.'.xml');
    }
}
