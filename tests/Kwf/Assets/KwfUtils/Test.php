<?php
class Kwf_Assets_KwfUtils_Test extends Kwf_Test_TestCase
{
    public function testDeps()
    {
        $l = new Kwf_Assets_KwfUtils_TestProviderList();
        $d = $l->findDependency('Kwf.Assets.KwfUtils.Test');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(18, count($array));
    }
}
