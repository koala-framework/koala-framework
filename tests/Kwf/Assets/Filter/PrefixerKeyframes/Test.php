<?php
class Kwf_Assets_Filter_PrefixerKeyframes_Test extends Kwf_Test_TestCase
{
    public function testRulePrefix()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/PrefixerKeyframes/test1.scss');
        $map = $d->getContentsPacked('en');

        $filter = new Kwf_Assets_Filter_Css_PrefixerKeyframes('myWebsite-');
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertContains('@keyframes myWebsite-mymove', $c);
    }

    public function testDeclarationPrefix()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/PrefixerKeyframes/test2.scss');
        $map = $d->getContentsPacked('en');

        $filter = new Kwf_Assets_Filter_Css_PrefixerKeyframes('myWebsite-');
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertContains('myWebsite-mymove', $c);
    }

    public function testExternalPrefix()
    {
        $d = new Kwf_Assets_Dependency_File_Scss('kwf/tests/Kwf/Assets/Filter/PrefixerKeyframes/test3.scss');
        $map = $d->getContentsPacked('en');

        $filter = new Kwf_Assets_Filter_Css_PrefixerKeyframes('myWebsite-');
        $map = $filter->filter($map);
        $c = $map->getFileContents();
        $this->assertNotContains('myWebsite-externalanimation', $c);
    }
}
