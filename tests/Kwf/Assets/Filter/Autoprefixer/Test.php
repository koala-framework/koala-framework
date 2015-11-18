<?php
class Kwf_Assets_Filter_Autoprefixer_Test extends Kwf_Test_TestCase
{
    public function testAddPrefix()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/Autoprefixer/test1.scss');
        $map = $d->getContentsPacked('en');

        $filter = new Kwf_Assets_Filter_Css_Autoprefixer();
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertContains('-webkit-transform', $c);
    }
    public function testRemovePrefix()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/Autoprefixer/test2.scss');
        $map = $d->getContentsPacked('en');

        $filter = new Kwf_Assets_Filter_Css_Autoprefixer();
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertNotContains('-moz-box-sizing', $c);
    }
}
