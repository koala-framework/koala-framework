<?php
class Kwf_Assets_Modernizr_DependencyTest extends Kwf_Test_TestCase
{
    public function test1()
    {
        $dep = new Kwf_Assets_Modernizr_Dependency();
        $dep->addFeature('CssAnimations');
        $this->assertContains('window.Modernizr=', $dep->getContents('en'));
        $this->assertContains('cssanimations', $dep->getContents('en'));
        $this->assertNotContains('csstransitions', $dep->getContents('en'));
    }

    public function test2()
    {
        $dep = new Kwf_Assets_Modernizr_Dependency();
        $dep->addFeature('CssTransitions');
        $this->assertContains('window.Modernizr=', $dep->getContents('en'));
        $this->assertNotContains('cssanimations', $dep->getContents('en'));
        $this->assertContains('csstransitions', $dep->getContents('en'));
    }
}
