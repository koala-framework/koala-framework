<?php
class Kwf_Assets_TrlJs_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $f = new Kwf_Assets_Dependency_File_Js('kwf/tests/Kwf/Assets/TrlJs/foo.js');
        $c = $f->getContents('de');
        $this->assertEquals("trl('Ja');", trim($c));
    }
}
