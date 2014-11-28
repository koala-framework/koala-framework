<?php
class Kwf_Assets_Modernizr_DependencyTest extends Kwf_Test_TestCase
{
    public function testIt()
    {
        $dep = new Kwf_Assets_Modernizr_Dependency();
        $dep->addFeature('CssAnimations');
        $this->assertContains(
            '/build-cssanimations-cssclasses-testprop-testallprops-domprefixes.js',
            $dep->getFileName()
        );

        $this->assertContains('window.Modernizr=', $dep->getContents('en'));
    }
}
