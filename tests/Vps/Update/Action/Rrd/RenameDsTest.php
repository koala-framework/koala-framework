<?php
/**
 * @group Update_Action
 * @group Update_Action_Rrd
 */
class Vps_Update_Action_Rrd_RenameDsTest extends Vps_Update_Action_Rrd_AbstractTest
{
    public function testRenameRrd()
    {
        $file = $this->_createTestFile();

        $action = new Vps_Update_Action_Rrd_RenameDs(array(
            'file' => $file,
            'name' => 'testx',
            'newName' => 'testxxx',
            'backup'=>false,
            'silent' => true
        ));
        $action->preUpdate();
        $action->update();
        $action->postUpdate();

        $cmd = "rrdtool dump $file > $file.xml";
        $this->_systemCheckRet($cmd);

        $xml = simplexml_load_file($file.'.xml');

        $this->assertEquals(2, count($xml->ds));
        $this->assertEquals('testxxx', trim($xml->ds[0]->name));
        $this->assertEquals('testxx', trim($xml->ds[1]->name));

        unlink($file);
        unlink($file.'.xml');
    }
}
