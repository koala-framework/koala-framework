<?php
abstract class Kwf_Assets_Dependency_Abstract
{
    const DEPENDENCY_TYPE_ALL = 'all';
    const DEPENDENCY_TYPE_REQUIRES = 'requires';
    const DEPENDENCY_TYPE_USES = 'uses';
    const DEPENDENCY_TYPE_COMMONJS = 'commonjs';
    protected $_dependencies = array();

    private $_deferLoad = false;
    private $_isCommonJsEntry = false;

    public function __construct()
    {
    }

    public function getDeferLoad()
    {
        return $this->_deferLoad;
    }

    public function setDeferLoad($v)
    {
        $this->_deferLoad = $v;
    }

    public function setIsCommonJsEntry($v)
    {
        $this->_isCommonJsEntry = $v;
        return $this;
    }

    public function isCommonJsEntry()
    {
        return $this->_isCommonJsEntry;
    }

    public function getContents($language)
    {
        return null;
    }

    public function getContentsPacked($language)
    {
        $contents = $this->getContents($language);
        return Kwf_SourceMaps_SourceMap::createEmptyMap($contents);
    }

    public function getContentsSource()
    {
        return array(
            'type' => 'contents',
            'contents' => $this->getContents('en'),
        );
    }

    public function getContentsSourceString()
    {
        $src = $this->getContentsSource();
        if ($src['type'] == 'file') {
            return file_get_contents($src['file']);
        } else if ($src['type'] == 'contents') {
            return $src['contents'];
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
    }

    public function usesLanguage()
    {
        return true;
    }

    public function setDependencies($type, $deps)
    {
        foreach ($deps as $dep) {
            if (!$dep) throw new Kwf_Exception("Not a valid dependency");
        }
        $this->_dependencies[$type] = $deps;
    }
    public function addDependencies($type, $deps)
    {
        foreach ($deps as $dep) {
            if (!$dep) throw new Kwf_Exception("Not a valid dependency");
        }
        if (!isset($this->_dependencies[$type])) $this->_dependencies[$type] = array();
        $this->_dependencies[$type] = array_merge($this->_dependencies[$type], $deps);
    }

    public function addDependency($type, $dep, $index = null)
    {
        if (!$dep) throw new Kwf_Exception("Not a valid dependency");
        if (!isset($this->_dependencies[$type])) $this->_dependencies[$type] = array();
        if (!is_null($index)) {
            if (isset($this->_dependencies[$type][$index])) {
//                 throw new Kwf_Exception("Dependency '$index' already set for '$this'");
            }
            $this->_dependencies[$type][$index] = $dep;
        } else {
            $this->_dependencies[$type][] = $dep;
        }
    }

    public function getDependencies($type)
    {
        if ($type == Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL) {
            $ret = array();
            foreach ($this->_dependencies as $i) {
                $ret = array_merge($ret, $i);
            }
            return $ret;
        }
        if (!isset($this->_dependencies[$type])) return array();
        return $this->_dependencies[$type];
    }


    public function getMimeType()
    {
        return null;
    }

    public function getWatchFiles()
    {
        return array();
    }
    public function getIncludeInPackage()
    {
        return true;
    }

    public function getMTime()
    {
        return null;
    }

    public function warmupCaches()
    {
    }

    public function __toString()
    {
        return get_class($this);
    }

    public function toDebug()
    {
        return get_class($this).': '.$this->__toString()."\n";
    }

    public function getRecursiveDependencies()
    {
        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_UniqueFilter(new Kwf_Assets_Dependency_Iterator_Recursive($this, Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL)), RecursiveIteratorIterator::CHILD_FIRST);
        $array = array();
        $array[] = $this;
        foreach ($it as $i) {
            if ($i !== $this) {
                $array[] = $i;
            }
        }
        return $array;
    }

    public function getRecursiveFiles()
    {
        $it = new RecursiveIteratorIterator(new Kwf_Assets_Dependency_Iterator_UniqueFilter(new Kwf_Assets_Dependency_Iterator_Recursive($this, Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL)), RecursiveIteratorIterator::CHILD_FIRST);
        $array = array();
        if ($this instanceof Kwf_Assets_Dependency_File) {
            $array[] = $this;
        }
        foreach ($it as $i) {
            if ($i instanceof Kwf_Assets_Dependency_File && $i !== $this) {
                $array[] = $i;
            }
        }
        return $array;
    }

    public function printDebugTree()
    {
        $this->_printDebugTree($this);
    }

    private function _printDebugTree($dep, $indent=0, &$processed=array())
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
            $this->_printDebugTree($i, $indent+1, $processed);
        }
        foreach ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS) as $i) {
            echo str_repeat(' ', $indent*2);
            echo 'commonjs ';
            $this->_printDebugTree($i, $indent+1, $processed);
        }
        foreach ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES) as $i) {
            echo str_repeat(' ', $indent*2);
            echo 'uses ';
            $this->_printDebugTree($i, $indent+1, $processed);
        }
    }

    public final function getFilteredUniqueDependencies($mimeType)
    {
        $processed = array();
        $ret = $this->_getFilteredUniqueDependenciesProcessDep($this, $mimeType, $processed, array(), true);
        //filter out deferred
        if ($mimeType == 'text/javascript; defer') {
            $nonDefer = array();
            $this->_getDependenciesNonDefer($this, $nonDefer, array());
            foreach ($ret as $k=>$i) {
                if (in_array($i, $nonDefer, true)) {
                    unset($ret[$k]);
                }
            }
        }
        return $ret;
    }

    private function _getDependenciesNonDefer($dep, &$processed, $stack)
    {
        if (in_array($dep, $processed, true)) {
            return;
        }
        $stack[] = $dep;
        if (!$dep->getDeferLoad()) {
            $requires = $dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL);
            foreach ($requires as $i) {
                if (!in_array($i, $stack, true)) {
                    $this->_getDependenciesNonDefer($i, $processed, $stack);
                }
            }
            if (in_array($dep, $processed, true)) {
                return;
            }

            $processed[] = $dep;
        }
    }

    private function _getFilteredUniqueDependenciesProcessDep($dep, $mimeType, &$processed, $stack, $includeSelf)
    {
        if (in_array($dep, $processed, true)) {
            return array();
        }
        $stack[] = $dep;

        $ret = array();
        if ($mimeType == 'text/javascript' && $dep->getDeferLoad()) {
            return array();
        }

        if ($mimeType == 'text/javascript; defer' && $dep->getDeferLoad()) {
            $mimeType = 'text/javascript; defer2';
        }

        foreach ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES) as $i) {
            if (!$i) throw new Kwf_Exception("$dep returned invalid dependency");
            if (!in_array($i, $stack, true)) {
                foreach ($this->_getFilteredUniqueDependenciesProcessDep($i, $mimeType, $processed, $stack, true) as $j) {
                    $ret[] = $j;
                }
            }
        }

        foreach ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS) as $i) {
            if (!$i) throw new Kwf_Exception("$dep returned invalid dependency");
            if (!in_array($i, $stack, true)) {
                foreach ($this->_getFilteredUniqueDependenciesProcessDep($i, $mimeType, $processed, $stack, false) as $j) {
                    $ret[] = $j;
                }
            }
        }

        if (in_array($dep, $processed, true)) {
            return $ret;
        }

        $processed[] = $dep;

        $mimeMatches = $dep->getMimeType() == $mimeType
            || ($mimeType == 'text/javascript; defer2' && $dep->getMimeType() == 'text/javascript');
        if ($includeSelf && $mimeMatches) {
            $ret[] = $dep;
        }

        foreach ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES) as $i) {
            if (!$i) throw new Kwf_Exception("$dep returned invalid dependency");
            foreach ($this->_getFilteredUniqueDependenciesProcessDep($i, $mimeType, $processed, array(), true) as $j) {
                $ret[] = $j;
            }
        }
        return $ret;
    }
}
