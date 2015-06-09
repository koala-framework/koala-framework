<?php
class Kwf_Assets_KwfUtils_Test extends Kwf_Test_TestCase
{
    public function testDeps()
    {
        $l = new Kwf_Assets_KwfUtils_TestProviderList();
        $d = $l->findDependency('Kwf.Assets.KwfUtils.Test');
        foreach ($d->getRecursiveFiles() as $i) {
            $array[] = $i->getFileNameWithType();
        }
        $this->assertContains('kwf/Kwf_js/CallOnContentReady.js', $array);
        $this->assertContains('kwf/Kwf_js/OnReadyExt.js', $array);
    }
}
