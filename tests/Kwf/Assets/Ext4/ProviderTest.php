<?php
class Kwf_Assets_Ext4_ProviderTest extends Kwf_Test_TestCase
{
    public function testObservable()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.util.Observable');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(25, count($array));
    }

    public function testDepOnObservable()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.util.ClickRepeater');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(26, count($array));
    }

    public function testOwnClsByIni()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Test');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(1, count($array));
    }

    public function testOwnClsByClassName()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Kwf.Assets.Ext4.TestCls');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(1, count($array));
    }

    public function testAtRequire()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('TestClsAtRequire');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(2, count($array));
    }

    public function testClsExtend()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('TestClsExtend');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(2, count($array));
    }

    public function testClsRecursion()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Kwf.Assets.Ext4.TestClsRecursion1');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(2, count($array));
    }

    public function testExtEventManager()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.EventManager');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(24, count($array));
    }

    public function testExtElement()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.dom.Element');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(45, count($array));
    }

    public function testExtFormat()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.util.Format');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(16, count($array));
    }

    public function testExtUtilHashMap()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.util.HashMap');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(26, count($array));
    }

    public function testExtUtilDelayedTask()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.util.DelayedTask');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(22, count($array));
    }

    public function testExtXTemplate()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.XTemplate');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(40, count($array));
    }

    public function testExtWindow()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.window.Window');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(197, count($array));
    }

    public function testRequire()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Kwf.Assets.Ext4.TestRequire');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(2, count($array));
    }

    public function testModel()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.data.Model');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(71, count($array));
    }

    public function testModelProxy()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Kwf.Assets.Ext4.TestModel');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(73, count($array));
    }

    public function testModelProxy2()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Kwf.Assets.Ext4.TestModel2');
        $array = $d->getRecursiveFiles();
        $this->assertEquals(73, count($array));
    }
}
