<?php

class Kwf_Assets_Filter_PrefixerFontface_Test extends Kwf_Test_TestCase
{
    public function testRulePrefix()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/PrefixerFontface/test1.scss');
        $map = $d->getContentsPacked('en');

        $filter = new Kwf_Assets_Filter_Css_PrefixerFontface('myWebsite-');
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertContains('myWebsite-MyWebFont', $c);
    }

    public function testDeclarationPrefix()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/PrefixerFontface/test2.scss');
        $map = $d->getContentsPacked('en');

        $filter = new Kwf_Assets_Filter_Css_PrefixerFontface('myWebsite-');
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertContains('myWebsite-MyWebFont', $c);
    }

    public function testExternalPrefix()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/PrefixerFontface/test3.scss');
        $map = $d->getContentsPacked('en');

        $filter = new Kwf_Assets_Filter_Css_PrefixerFontface('myWebsite-');
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertNotContains('myWebsite-', $c);
    }
}
