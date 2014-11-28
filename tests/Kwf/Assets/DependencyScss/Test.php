<?php
class Kwf_Assets_DependencyScss_Test extends Kwf_Test_TestCase
{
    public function testBasic()
    {
        $f = new Kwf_Assets_Dependency_File_Scss(dirname(__FILE__).'/file1.scss');
        $c = $f->getContents('en');
        $this->assertEquals('body { width: 100px; }', trim($c));
    }

    public function testVar()
    {
        $f = new Kwf_Assets_Dependency_File_Scss(dirname(__FILE__).'/file2.scss');
        $c = $f->getContents('en');
        $this->assertEquals('body { height: 50px; }', trim($c));
    }

    public function testCompass()
    {
        $f = new Kwf_Assets_Dependency_File_Scss(dirname(__FILE__).'/file3.scss');
        $c = $f->getContents('en');
        $this->assertContains("body { -webkit-border-radius: 3px; -moz-border-radius: 3px; -ms-border-radius: 3px; -o-border-radius: 3px; border-radius: 3px; }", trim($c));
    }
}
