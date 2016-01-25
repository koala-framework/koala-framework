<?php
class Kwf_Assets_CommonJs_Provider extends Kwf_Assets_Provider_Abstract
{
    private $_parsed = array();

    public function __construct()
    {
    }

    public function getDependency($dependencyName)
    {
        return null;
    }

    public function getDependenciesForDependency($dependency)
    {
        if ($dependency->getMimeType() != 'text/javascript' && $dependency->getMimeType() != 'text/javascript; defer') {
            return array();
        }
        if (!$dependency->isCommonJsEntry()) {
            return array();
        }
        $ret = array(
            Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS => $this->_parseDependencies($dependency)
        );
        return $ret;
    }

    private function _parseDependencies($dependency)
    {
        if (in_array($dependency, $this->_parsed, true)) return array();

        $this->_parsed[] = $dependency;

        $ret = array();
        $deps = array();

        $src = $dependency->getContentsSource();
        if ($src['type'] == 'file') {
            $deps = Kwf_Assets_CommonJs_Parser::parse($src['file']);
        } else if ($src['type'] == 'contents') {
            $temp = tempnam('temp/', 'commonjs');
            file_put_contents($temp, $src['contents']);
            $deps = Kwf_Assets_CommonJs_Parser::parse($temp);
            unlink($temp);
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }

        foreach ($deps as $depName) {
            $dep = $depName;
            if (substr($dep, 0, 2) == './') {
                $fn = $dependency->getFileNameWithType();
                $dir = substr($fn, 0, strrpos($fn, '/')+1);
                $dep = $dir . substr($dep, 2);
            } else if (substr($dep, 0, 3) == '../') {
                $fn = $dependency->getFileNameWithType();
                $dir = substr($fn, 0, strrpos($fn, '/'));
                while (substr($dep, 0, 3) == '../') {
                    $dep = substr($dep, 3);
                    $dir = substr($dir, 0, strrpos($dir, '/'));
                }
                $dep = $dir . '/'. $dep;
            }
            $d = $this->_providerList->findDependency($dep);
            if (!$d) throw new Kwf_Exception("Can't resolve dependency: require '$depName' for $dependency");
            $ret[$depName] = $d;

            $requires = $d->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES);
            foreach ($requires as $index=>$r) {
                if ($r->getMimeType() == 'text/javascript') {
                    unset($requires[$index]);
                }
            }
            $d->setDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $requires);

            foreach ($this->_parseDependencies($d) as $index=>$i) {
                $d->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS, $i, $index);
            }

        }
        return $ret;
    }
}
