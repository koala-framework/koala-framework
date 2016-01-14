<?php
class Kwf_Assets_Filter_UniquePrefix_Test extends Kwf_Test_TestCase
{
    public function testAddPrefix()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/UniquePrefix/test1.scss');
        $map = $d->getContentsPacked('en');

        $filter = new Kwf_Assets_Filter_Css_UniquePrefix('myWebsite-');
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertEquals('.myWebsite-test{color:red}', trim($c));
    }

    public function testRemovePrefix()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/UniquePrefix/test1.scss');
        $map = $d->getContentsPacked('en');

        $filter = new Kwf_Assets_Filter_Css_UniquePrefix('');
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertEquals('.test{color:red}', trim($c));
    }
}
