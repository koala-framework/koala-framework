<?php
/**
 * @group Update_Action
 * @group Update_Action_Rrd
 * @group Update_Action_Rrd_DropDs
 */
class Vps_Update_Action_Rrd_DropDsTest extends Vps_Update_Action_Rrd_AbstractTest
{

    public function testRenameRrd()
    {
        $file = $this->_createTestFile();

        $action = new Vps_Update_Action_Rrd_DropDs(array(
            'file' => $file,
            'name' => 'testx',
            'backup'=>false,
            'silent' => true
        ));
        $action->preUpdate();
        $action->update();
        $action->postUpdate();

        $cmd = "rrdtool dump $file > $file.xml";
        $this->_systemCheckRet($cmd);

        $xml = simplexml_load_file($file.'.xml');

        $this->assertEquals(1, count($xml->ds));
        $this->assertEquals('testxx', trim($xml->ds[0]->name));

        unlink($file);
        unlink($file.'.xml');
    }
}
