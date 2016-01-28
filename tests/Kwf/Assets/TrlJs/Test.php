<?php
class Kwf_Assets_TrlJs_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        $trlElements = array();
        $trlElements['kwf']['de']['Yes-'] = 'Ja';
        $trlElements['kwf']['de']['.-decimal separator'] = ',';
        $trlElements['kwf']['de'][',-thousands separator'] = '.';
        Kwf_Trl::getInstance()->setTrlElements($trlElements);
    }

    public function testIt()
    {
        $f = new Kwf_Assets_Dependency_File_Js(new Kwf_Assets_Dependency_EmptyProviderList(), 'kwf/tests/Kwf/Assets/TrlJs/foo.js');
        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals("trl('Ja');", trim($c));
    }

    public function testWithContext()
    {
        $f = new Kwf_Assets_Dependency_File_Js(new Kwf_Assets_Dependency_EmptyProviderList(), 'kwf/tests/Kwf/Assets/TrlJs/withContext.js');
        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals("trl(',');", trim($c));

        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals("trl(\",\");", trim($c));

        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals("trl('.');", trim($c));

        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals("trl(\".\");", trim($c));
    }

    public function testWithContext2()
    {
        $f = new Kwf_Assets_Dependency_File_Js(new Kwf_Assets_Dependency_EmptyProviderList(), 'kwf/tests/Kwf/Assets/TrlJs/withContext2.js');
        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals("trl('.');", trim($c));

        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals("trl(\".\");", trim($c));

        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals("trl(',');", trim($c));

        $c = $f->getContentsPacked()->getFileContents();
        $this->assertEquals("trl(\",\");", trim($c));
    }
}
