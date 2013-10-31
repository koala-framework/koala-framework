<?php
class Kwf_Assets_Ext4_ProviderTest extends Kwf_Test_TestCase
{
    private function _getFiles(Kwf_Assets_Dependency_Abstract $d)
    {
        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_UniqueFilter(new Kwf_Assets_Dependency_Iterator_Recursive($d, Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL)), RecursiveIteratorIterator::CHILD_FIRST);
        $array = array();
        if ($d instanceof Kwf_Assets_Dependency_File) {
            $array[] = $d;
        }
        foreach ($it as $i) {
            if ($i instanceof Kwf_Assets_Dependency_File && $i !== $d) {
                $array[] = $i;
            }
        }
        return $array;
    }

    private function _dbg($dep, $indent=0, &$processed=array())
    {
        echo $dep->toDebug();
        if (in_array($dep, $processed, true)) {
            echo str_repeat(' ', ($indent+1)*2)."(recursion)\n";
            return;
        }
        $processed[] = $dep;
        foreach ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES) as $i) {
            echo str_repeat(' ', $indent*2);
            echo 'requires ';
            $this->_dbg($i, $indent+1, $processed);
        }
        foreach ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES) as $i) {
            echo str_repeat(' ', $indent*2);
            echo 'uses ';
            $this->_dbg($i, $indent+1, $processed);
        }
    }

    public function testObservable()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.util.Observable');
        $array = $this->_getFiles($d);
        $this->assertEquals(25, count($array));
    }

    public function testDepOnObservable()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.util.ClickRepeater');
        $array = $this->_getFiles($d);
        $this->assertEquals(26, count($array));
    }

    public function testOwnClsByIni()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Test');
        $array = $this->_getFiles($d);
        $this->assertEquals(1, count($array));
    }

    public function testOwnClsByClassName()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Kwf.Assets.Ext4.TestCls');
        $array = $this->_getFiles($d);
        $this->assertEquals(1, count($array));
    }

    public function testAtRequire()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('TestClsAtRequire');
        $array = $this->_getFiles($d);
        $this->assertEquals(2, count($array));
    }

    public function testClsExtend()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('TestClsExtend');
        $array = $this->_getFiles($d);
        $this->assertEquals(2, count($array));
    }

    public function testClsRecursion()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Kwf.Assets.Ext4.TestClsRecursion1');
        $array = $this->_getFiles($d);
        $this->assertEquals(2, count($array));
    }

    public function testExtEventManager()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.EventManager');
        $array = $this->_getFiles($d);
        $this->assertEquals(24, count($array));
    }

    public function testExtElement()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.dom.Element');
        $array = $this->_getFiles($d);
        $this->assertEquals(45, count($array));
    }

    public function testExtFormat()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.util.Format');
        $array = $this->_getFiles($d);
        $this->assertEquals(16, count($array));
    }

    public function testExtUtilHashMap()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.util.HashMap');
        $array = $this->_getFiles($d);
        $this->assertEquals(26, count($array));
    }

    public function testExtUtilDelayedTask()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.util.DelayedTask');
        $array = $this->_getFiles($d);
        $this->assertEquals(22, count($array));
    }

    public function testExtXTemplate()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.XTemplate');
        $array = $this->_getFiles($d);
        $this->assertEquals(40, count($array));
    }

    public function testExtWindow()
    {
        $l = new Kwf_Assets_Ext4_TestProviderList();
        $d = $l->findDependency('Ext4.window.Window');
        $array = $this->_getFiles($d);
        $this->assertEquals(196, count($array));
    }
}
