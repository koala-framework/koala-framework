<?php
class Kwf_Assets_Filter_Ie8_Test extends Kwf_Test_TestCase
{
    public function testOnlyIe8()
    {
        $d = new Kwf_Assets_Dependency_File_Scss(new Kwf_Assets_Dependency_EmptyProviderList(), 'kwf/tests/Kwf/Assets/Filter/Ie8/test1.scss');
        $map = $d->getContentsPacked();
        $map->setMimeType('text/css');

        $filter = new Kwf_Assets_Filter_Css_Ie8Only(true);
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $c = str_replace(array(" ", "\n"), '', $c);
        $this->assertEquals('p{color:red}', $c);
    }

    public function testNotIe8()
    {
        $d = new Kwf_Assets_Dependency_File_Scss(new Kwf_Assets_Dependency_EmptyProviderList(), 'kwf/tests/Kwf/Assets/Filter/Ie8/test1.scss');
        $map = $d->getContentsPacked();
        $map->setMimeType('text/css');

        $filter = new Kwf_Assets_Filter_Css_Ie8Remove(false);
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $c = str_replace(array(" ", "\n"), '', $c);
        $this->assertEquals('p{color:green}@mediaprint{p{color:blue}}', $c);
    }
}
