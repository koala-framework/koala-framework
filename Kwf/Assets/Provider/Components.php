<?php
class Kwf_Assets_Provider_Components extends Kwf_Assets_Provider_Abstract
{
    private $_rootComponentClass;
    private $_componentFiles = array();

    public function __construct($rootComponentClass)
    {
        $this->_rootComponentClass = $rootComponentClass;
    }

    private function _createDependencyForFile($file, $isCommonJsEntry)
    {
        if (!isset($this->_componentFiles[$file])) {
            $this->_componentFiles[$file] = Kwf_Assets_Dependency_File::createDependency($file, $this->_providerList);
            $this->_componentFiles[$file]->setIsCommonJsEntry($isCommonJsEntry);
        }
        return $this->_componentFiles[$file];
    }

    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'Components') {
            $ret = array();
            $nonDeferDep = array();
            $files = Kwf_Component_Abstract_Admin::getComponentFiles($this->_rootComponentClass, array(
                'css' => array('filename'=>'Web', 'ext'=>'css', 'returnClass'=>false, 'multiple'=>true),
                'printcss' => array('filename'=>'Web', 'ext'=>'printcss', 'returnClass'=>false, 'multiple'=>true),
                'scss' => array('filename'=>'Web', 'ext'=>'scss', 'returnClass'=>false, 'multiple'=>true),
            ));
            foreach ($files as $i) {
                foreach ($i as $j) {
                    $cwd = str_replace(DIRECTORY_SEPARATOR, '/', getcwd());
                    if (substr($j, 0, 3) == '../') {
                        $cwd = substr($cwd, 0, strrpos($cwd, '/'));
                        $j = substr($j, 3);
                    }
                    $j = $cwd.'/'.$j;
                    $jj = Kwf_Assets_Dependency_File::getPathWithTypeByFileName($j);
                    if (!$jj) {
                        throw new Kwf_Exception("Can't find path type for '$j'");
                    }
                    $nonDeferDep[] = $this->_createDependencyForFile($jj, true);
                }
            }
            if ($nonDeferDep) {
                $nonDeferDep = new Kwf_Assets_Dependency_Dependencies($nonDeferDep, 'Web');
                $ret[] = $nonDeferDep;
            }

            $componentClasses = $this->_getRecursiveChildClasses($this->_rootComponentClass);

            foreach ($componentClasses as $class) {

                $nonDeferDep = $this->_getComponentSettingDependencies($class, 'assets', true);

                $deferDep = $this->_getComponentSettingDependencies($class, 'assetsDefer', true);

                //alle dateien der vererbungshierache includieren
                $files = Kwc_Abstract::getSetting($class, 'componentFiles');
                $componentCssFiles = array();
                foreach (array_merge($files['css'], $files['printcss'], $files['js'], $files['masterCss']) as $f) {
                    $componentCssFiles[] = $f;
                }
                //reverse damit css von weiter unten in der vererbungshierachie überschreibt
                $componentCssFiles = array_reverse($componentCssFiles);
                foreach ($componentCssFiles as $i) {
                    $i = getcwd().'/'.$i;
                    $i = Kwf_Assets_Dependency_File::getPathWithTypeByFileName($i);
                    if (!isset($this->_componentFiles[$i])) {
                        $addedFiles[] = $i;
                        $dep = $this->_createDependencyForFile($i, true);
                        if (substr($i, -8) == 'defer.js') {
                            $deferDep[] = $dep;
                        } else {
                            $nonDeferDep[] = $dep;
                        }
                    }
                }

                if ($deferDep) {
                    $deferDep = new Kwf_Assets_Dependency_Dependencies($deferDep, $class.' defer');
                    $deferDep->setDeferLoad(true);
                    $ret[] = $deferDep;
                }
                if ($nonDeferDep) {
                    $nonDeferDep = new Kwf_Assets_Dependency_Dependencies($nonDeferDep, $class);
                    $ret[] = $nonDeferDep;
                }
            }
            return new Kwf_Assets_Dependency_Dependencies($ret, $dependencyName);

        } else if ($dependencyName == 'ComponentsAdmin') {
            $ret = array();
            $componentClasses = $this->_getRecursiveChildClasses($this->_rootComponentClass);
            foreach ($componentClasses as $class) {
                $ret = array_merge($ret, $this->_getComponentSettingDependencies($class, 'assetsAdmin', false));
            }
            return new Kwf_Assets_Dependency_Dependencies($ret, $dependencyName);
        } else if ($dependencyName == 'FrontendCore') {
            $deps = array();

            $dep = new Kwf_Assets_Dependency_File_Js('kwf/commonjs/frontend-core.js');
            $dep->setIsCommonJsEntry(true);
            $deps[] = $dep;

            $dep = new Kwf_Assets_Dependency_File_Js('kwf/commonjs/frontend-core.defer.js');
            $dep->setIsCommonJsEntry(true);
            $dep->setDeferLoad(true);
            $deps[] = $dep;

            return new Kwf_Assets_Dependency_Dependencies($deps, 'FrontendCore');
        }
        return null;
    }

    private function _getComponentSettingDependencies($class, $setting, $isCommonJsEntry)
    {
        $ret = array();
        $assets = Kwc_Abstract::getSetting($class, $setting);
        if (!is_array($assets['dep'])) {
            throw new Kwf_Exception("Invalid dep dependency for '$class'");
        }
        foreach ($assets['dep'] as $i) {
            $d = $this->_providerList->findDependency(trim($i));
            if (!$d) {
                throw new Kwf_Exception("Can't find dependency '$i'");
            }
            $ret[] = $d;
        }
        foreach ($assets['files'] as $i) {
            if (!is_object($i)) {
                $i = $this->_createDependencyForFile($i, $isCommonJsEntry);
            }
            $ret[] = $i;
        }
        return $ret;
    }

    private function _getRecursiveChildClasses($class, &$processedComponents = array())
    {
        $processedComponents[] = $class;

        $ret = array();

        $ret[] = $class;

        $classes = Kwc_Abstract::getChildComponentClasses($class);
        $classes = array_merge($classes, Kwc_Abstract::getSetting($class, 'plugins'));
        foreach (Kwc_Abstract::getSetting($class, 'generators') as $g) {
            if (isset($g['plugins'])) {
                $classes = array_merge($classes, $g['plugins']);
            }
        }

        foreach ($classes as $i) {
            if ($i && !in_array($i, $processedComponents)) {
                $ret = array_merge($ret, $this->_getRecursiveChildClasses($i, $processedComponents));
            }
        }

        return $ret;
    }
}
