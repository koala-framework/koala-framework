<?php
/**
 * @group Update_Action
 */
class Vps_Update_Action_Rrd_RenameDsTest extends Vps_Update_Action_Rrd_AbstractTest
{
    public function testRenameRrd()
    {
        $file = $this->_createTestFile();

        $action = new Vps_Update_Action_Rrd_RenameDs(array(
            'file' => $file,
            'name' => 'test1',
            'newName' => 'test3'
        ));
        $action->update();

        $cmd = "rrdtool dump $file > $file.xml";
        $this->_systemCheckRet($cmd);

        $xml = simplexml_load_file($file.'.xml');

        $this->assertEquals(2, count($xml->ds));
        $this->assertEquals('test3', trim($xml->ds[0]->name));
        $this->assertEquals('test2', trim($xml->ds[1]->name));

        unlink($file);
        unlink($file.'.xml');
    }
}
