<?php
class Kwf_Assets_Components_Provider extends Kwf_Assets_Provider_Abstract
{
    private $_rootComponentClass;
    private $_fileDependencies = array();
    private $_componentClassesCache;
    public function __construct($rootComponentClass)
    {
        $this->_rootComponentClass = $rootComponentClass;
    }

    private function _createDependencyForFile($file, $isCommonJsEntry)
    {
        $ret = Kwf_Assets_Dependency_File::createDependency($file, $this->_providerList);
        $ret->setIsCommonJsEntry($isCommonJsEntry);
        return $ret;
    }

    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'Components') {
            $ret = array();
            $nonDeferDep = array();
            $files = Kwf_Component_Abstract_Admin::getComponentFiles($this->_rootComponentClass, array(
                'css' => array('filename'=>'Web', 'ext'=>'css', 'returnClass'=>false, 'multiple'=>true),
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
                    $jj = Kwf_Assets_Dependency_File::getPathWithTypeByFileName($this->_providerList, $j);
                    if (!$jj) {
                        throw new Kwf_Exception("Can't find path type for '$j'");
                    }
                    $nonDeferDep[] = $this->_createDependencyForFile($jj, true);
                }
            }
            if ($nonDeferDep) {
                $nonDeferDep = new Kwf_Assets_Dependency_Dependencies($this->_providerList, $nonDeferDep, 'Web');
                $ret[] = $nonDeferDep;
            }

            foreach ($this->_getComponentClassesPackages()['Frontend'] as $c) {
                if (!Kwc_Abstract::getFlag($c, 'assetsPackage')) {
                    $d = $this->_providerList->findDependency('Component-'.$c);
                    if (!$d) throw new Kwf_Exception("Didn't get dependency 'Component-$c'");
                    $ret[] = $d;
                }
            }
            return new Kwf_Assets_Dependency_Dependencies($this->_providerList, $ret, $dependencyName);

        } else if (substr($dependencyName, 0, 17) == 'ComponentsPackage') {

            $ret = array();
            $assetsPackageName = substr($dependencyName, 17);

            foreach ($this->_getComponentClassesPackages()[$assetsPackageName] as $c) {
                $d = $this->_providerList->findDependency('Component-'.$c);
                if (!$d) throw new Kwf_Exception("Didn't get dependency 'Component-$c'");
                $ret[] = $d;
            }
            return new Kwf_Assets_Dependency_Dependencies($this->_providerList, $ret, $dependencyName);

        } else if ($dependencyName == 'ComponentsAdmin') {
            $ret = array();
            foreach (Kwc_Abstract::getComponentClasses() as $class) {
                //dep
                $ret = array_merge($ret, $this->_getComponentSettingDependenciesDep($class, 'assetsAdmin'));

                //files
                $assets = Kwc_Abstract::getSetting($class, 'assetsAdmin');
                foreach ($assets['files'] as $file) {
                    if (!isset($this->_fileDependencies[$file])) {
                        $this->_fileDependencies[$file] = Kwf_Assets_Dependency_File::createDependency($file, $this->_providerList);
                    }
                    $ret[] = $this->_fileDependencies[$file];
                }
            }
            return new Kwf_Assets_Dependency_Dependencies($this->_providerList, $ret, $dependencyName);
        } else if ($dependencyName == 'FrontendCore') {
            $deps = array();

            $dep = new Kwf_Assets_Dependency_File_Js($this->_providerList, 'kwf/commonjs/frontend-core.js');
            $dep->setIsCommonJsEntry(true);
            $deps[] = $dep;

            $dep = new Kwf_Assets_Dependency_File_Js($this->_providerList, 'kwf/commonjs/frontend-core.defer.js');
            $dep->setIsCommonJsEntry(true);
            $dep->setDeferLoad(true);
            $deps[] = $dep;

            return new Kwf_Assets_Dependency_Dependencies($this->_providerList, $deps, 'FrontendCore');
        } else if (substr($dependencyName, 0, 10) == 'Component-') {

            $class = substr($dependencyName, 10);

            $ret = array();

            $deps = $this->_getComponentSettingDependenciesDep($class, 'assetsDefer', true);
            if ($deps) {
                $deps = new Kwf_Assets_Dependency_Dependencies($this->_providerList, $deps, $class.'-deps-defer');
                $deps->setDeferLoad(true);
                $ret[] = $deps;
            }

            $deps = $this->_getComponentSettingDependenciesDep($class, 'assets', true);
            if ($deps) {
                $deps = new Kwf_Assets_Dependency_Dependencies($this->_providerList, $deps, $class.'-deps');
                $ret[] = $deps;
            }

            $deps = array();
            foreach ($this->_getComponentSettingDependenciesFiles($class, 'assets', true) as $dep) {
                if ($dep instanceof Kwf_Assets_Dependency_File && preg_match('#Master\.[a-z]+$#', $dep->getFileNameWithType())) {
                    $deps[] = array(
                        'dep' => $dep,
                        'master' => true,
                        'defer' => false
                    );
                } else {
                    $deps[] = array(
                        'dep' => $dep,
                        'master' => false,
                        'defer' => false
                    );
                }
            }
            foreach ($this->_getComponentSettingDependenciesFiles($class, 'assetsDefer', true) as $dep) {
                if ($dep instanceof Kwf_Assets_Dependency_File && preg_match('#Master\.[a-z\.]+$#', $dep->getFileNameWithType())) {
                    $deps[] = array(
                        'dep' => $dep,
                        'master' => true,
                        'defer' => true
                    );
                } else {
                    $deps[] = array(
                        'dep' => $dep,
                        'master' => false,
                        'defer' => true
                    );
                }
            }

            //alle dateien der vererbungshierache includieren
            $files = Kwc_Abstract::getSetting($class, 'componentFiles');
            $componentCssFiles = array();
            foreach (array_merge($files['css'], $files['js']) as $f) {
                $componentCssFiles[] = $f;
            }

            //reverse damit css von weiter unten in der vererbungshierachie überschreibt
            $componentCssFiles = array_reverse($componentCssFiles);
            foreach ($componentCssFiles as $i) {
                $dep = $this->_createDependencyForFile($i, true);
                if (substr($i, -8) == 'defer.js') {
                    $deps[] = array(
                        'dep' => $dep,
                        'master' => false,
                        'defer' => true
                    );
                } else {
                    $deps[] = array(
                        'dep' => $dep,
                        'master' => false,
                        'defer' => false
                    );
                }
                if ($dep instanceof Kwf_Assets_Dependency_File_Scss) {
                    if ($scssConfig = Kwc_Admin::getInstance($class)->getScssConfig()) {
                        $dep->setConfig($scssConfig, Kwc_Admin::getInstance($class)->getScssConfigMasterFiles());
                    }
                }
            }

            //reverse damit css von weiter unten in der vererbungshierachie überschreibt
            $componentCssFiles = array_reverse($files['masterCss']);
            foreach ($componentCssFiles as $i) {
                $dep = $this->_createDependencyForFile($i, true);
                if (substr($i, -8) == 'defer.js') {
                    $deps[] = array(
                        'dep' => $dep,
                        'master' => true,
                        'defer' => true
                    );
                } else {
                    $deps[] = array(
                        'dep' => $dep,
                        'master' => true,
                        'defer' => false
                    );
                }
            }


            //css, not master
            $matchingDeps = array();
            foreach ($deps as $i) {
                if ($i['dep']->getMimeType() == 'text/css' && $i['master'] == false) {
                    $matchingDeps[] = $i['dep'];
                }
            }
            if ($matchingDeps) {
                $ret[] = new Kwf_Assets_Components_Dependency_Css($this->_providerList, $class, $matchingDeps, false, $class.'-css');
            }

            //css, master
            $matchingDeps = array();
            foreach ($deps as $i) {
                if ($i['dep']->getMimeType() == 'text/css' && $i['master'] == true) {
                    $matchingDeps[] = $i['dep'];
                }
            }
            if ($matchingDeps) {
                $ret[] = new Kwf_Assets_Components_Dependency_Css($this->_providerList, $class, $matchingDeps, true, $class.'-master-css');
            }

            //js, not master, not defer
            $matchingDeps = array();
            foreach ($deps as $i) {
                if ($i['dep']->getMimeType() != 'text/css' && $i['master'] == false && $i['defer'] == false) {
                    $matchingDeps[] = $i['dep'];
                }
            }
            if ($matchingDeps) {
                $ret[] = new Kwf_Assets_Components_Dependency_Js($this->_providerList, $class, $matchingDeps, false, $class.'-js');
            }

            //js, master, not defer
            $matchingDeps = array();
            foreach ($deps as $i) {
                if ($i['dep']->getMimeType() != 'text/css' && $i['master'] == true && $i['defer'] == false) {
                    $matchingDeps[] = $i['dep'];
                }
            }
            if ($matchingDeps) {
                $ret[] = new Kwf_Assets_Components_Dependency_Js($this->_providerList, $class, $matchingDeps, true, $class.'-master-js');
            }

            //js, not master, defer
            $matchingDeps = array();
            foreach ($deps as $i) {
                if ($i['dep']->getMimeType() != 'text/css' && $i['master'] == false && $i['defer'] == true) {
                    $matchingDeps[] = $i['dep'];
                }
            }
            if ($matchingDeps) {
                $dep = new Kwf_Assets_Components_Dependency_Js($this->_providerList, $class, $matchingDeps, false, $class.'-defer-js');
                $dep->setDeferLoad(true);
                $ret[] = $dep;
            }

            //js, master, defer
            $matchingDeps = array();
            foreach ($deps as $i) {
                if ($i['dep']->getMimeType() != 'text/css' && $i['master'] == true && $i['defer'] == true) {
                    $matchingDeps[] = $i['dep'];
                }
            }
            if ($matchingDeps) {
                $dep = new Kwf_Assets_Components_Dependency_Js($this->_providerList, $class, $matchingDeps, true, $class.'-master-defer-js');
                $dep->setDeferLoad(true);
                $ret[] = $dep;
            }

            return new Kwf_Assets_Dependency_Dependencies($this->_providerList, $ret, $dependencyName);
        }
        return null;
    }

    private function _getComponentSettingDependenciesFiles($class, $setting, $isCommonJsEntry)
    {
        $ret = array();
        $assets = Kwc_Abstract::getSetting($class, $setting);
        foreach ($assets['files'] as $i) {
            if (!is_object($i)) {
                $i = $this->_createDependencyForFile($i, $isCommonJsEntry);
            }
            $ret[] = $i;
        }
        return $ret;
    }

    private function _getComponentSettingDependenciesDep($class, $setting)
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
        return $ret;
    }

    private function _getComponentClassesPackages()
    {
        $frontendPackageClasses = array();
        $componentClassesWithoutParam = array();
        foreach ($this->_getRecursiveChildClasses($this->_rootComponentClass, '') as $c) {
            $cWithoutParam = strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c;
            if (!in_array($cWithoutParam, $componentClassesWithoutParam)) {
                $componentClassesWithoutParam[] = $cWithoutParam; //only add one per component class without parameter
                $frontendPackageClasses[] = $c;
            }
        }

        $otherPackageClasses = array();
        $otherPackageClassesWithoutParam = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            $cWithoutParam = strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c;
            if (in_array($cWithoutParam, $componentClassesWithoutParam)) {
                continue;
            }
            $packageName = Kwc_Abstract::getFlag($c, 'assetsPackage');
            if ($packageName) {
                if (!isset($otherPackageClasses[$packageName])) {
                    $otherPackageClasses[$packageName] = array();
                }
                if (!isset($otherPackageClassesWithoutParam[$packageName])) {
                    $otherPackageClassesWithoutParam[$packageName] = array();
                }
                foreach ($this->_getRecursiveChildClasses($c, $packageName) as $i) {
                    $iWithoutParam = strpos($i, '.') ? substr($i, 0, strpos($i, '.')) : $i;
                    if (!in_array($iWithoutParam, $componentClassesWithoutParam) && !in_array($iWithoutParam, $otherPackageClassesWithoutParam[$packageName])) {
                        $otherPackageClasses[$packageName][] = $i;
                        $otherPackageClassesWithoutParam[$packageName][] = $iWithoutParam;
                        foreach ($otherPackageClasses as $pName => $classes) {
                            if ($pName == $packageName) continue;
                            if (in_array($i, $classes)) {
                                throw new Kwf_Exception("Component '$i' is in package '$pName' and '$packageName'");
                            }
                        }
                    }
                }
            }
        }

        $otherPackageClasses['Frontend'] = $frontendPackageClasses;


        return $otherPackageClasses;
    }

    private function _getRecursiveChildClasses($class, $assetsPackage, &$processedComponents = array())
    {
        $processedComponents[] = $class;

        $ret = array();

        $cPackage = Kwc_Abstract::getFlag($class, 'assetsPackage');
        if (!$assetsPackage) {
            if ($cPackage) {
                return $ret;
            }
        } else {
            if ($cPackage) {
                if ($cPackage != $assetsPackage) {
                    return $ret;
                }
            }
        }
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
                $ret = array_merge($ret, $this->_getRecursiveChildClasses($i, $assetsPackage, $processedComponents));
            }
        }

        return $ret;
    }
}
