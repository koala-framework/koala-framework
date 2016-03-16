<?php
class Kwf_Assets_Modernizr_DependencyTest extends Kwf_Test_TestCase
{
    public function test1()
    {
        $dep = new Kwf_Assets_Modernizr_Dependency();
        $dep->addFeature('CssAnimations');
        $this->assertContains('e.Modernizr=Modernizr', $dep->getContentsPacked()->getFileContents());
        $this->assertContains('cssanimations', $dep->getContentsPacked()->getFileContents());
        $this->assertNotContains('csstransitions', $dep->getContentsPacked()->getFileContents());
    }

    public function test2()
    {
        $dep = new Kwf_Assets_Modernizr_Dependency();
        $dep->addFeature('CssTransitions');
        $this->assertContains('e.Modernizr=Modernizr', $dep->getContentsPacked()->getFileContents());
        $this->assertNotContains('cssanimations', $dep->getContentsPacked()->getFileContents());
        $this->assertContains('csstransitions', $dep->getContentsPacked()->getFileContents());
    }
}
