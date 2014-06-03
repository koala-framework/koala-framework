<?php
class Kwf_Assets_TrlJs_Test extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $f = new Kwf_Assets_Dependency_File_Js('kwf/tests/Kwf/Assets/TrlJs/foo.js');
        $c = $f->getContents('de');
        $this->assertEquals("trl('Ja');", trim($c));
    }

    public function testWithContext()
    {
        $f = new Kwf_Assets_Dependency_File_Js('kwf/tests/Kwf/Assets/TrlJs/withContext.js');
        $c = $f->getContents('de');
        $this->assertEquals("trl(',')", trim($c));

        $c = $f->getContents('en');
        $this->assertEquals("trl('.')", trim($c));
    }
}
