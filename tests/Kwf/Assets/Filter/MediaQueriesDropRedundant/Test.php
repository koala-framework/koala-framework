<?php

class Kwf_Assets_Filter_MediaQueriesDropRedundant_Test extends Kwf_Test_TestCase
{
    public function testMinWidth()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/MediaQueriesDropRedundant/test1.scss');
        $map = $d->getContentsPacked();

        $filter = new Kwf_Assets_Filter_Css_MediaQueriesDropRedundant();
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertEquals(1, substr_count($c, 'color:red'));
    }

    public function testMaxWidth()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/MediaQueriesDropRedundant/test2.scss');
        $map = $d->getContentsPacked();

        $filter = new Kwf_Assets_Filter_Css_MediaQueriesDropRedundant();
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertEquals(1, substr_count($c, 'height:100px'));
    }

}
