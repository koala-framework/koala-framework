<?php
class Kwf_Controller_Action_Cli_Web_ComponentIntrospectionController extends Kwf_Controller_Action
{
    public function getComponentClassesAction()
    {
        $ret = Kwc_Abstract::getComponentClasses();
        echo json_encode($ret);
        exit;
    }

    protected function _getKwcClass($class, $master)
    {
        $kwcClass = Kwf_Component_Abstract::formatRootElementClass($class, '');
        if ($master) $kwcClass .= 'Master';
        if (Kwf_Config::getValue('application.uniquePrefix')) {
            $kwcClass = str_replace('kwfUp-', Kwf_Config::getValue('application.uniquePrefix').'-', $kwcClass);
        } else {
            $kwcClass = str_replace('kwfUp-', '', $kwcClass);
        }
        return $kwcClass;
    }

    public function getComponentAssetsAction()
    {
        $this->_rootComponentClass = Kwf_Component_Data_Root::getComponentClass();

        $componentCssFiles = array();
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            $componentCssFiles[$class] = array(
                'files' => array(),
                'kwcClass' => $this->_getKwcClass($class, false),
                'kwcClassMaster' => $this->_getKwcClass($class, true),
                'assets' => Kwc_Abstract::getSetting($class, 'assets'),
                'assetsDefer' => Kwc_Abstract::getSetting($class, 'assetsDefer'),
                'assetsAdmin' => Kwc_Abstract::getSetting($class, 'assetsAdmin'),
            );
            $files = Kwc_Abstract::getSetting($class, 'componentFiles');
            foreach (array_merge($files['css'], $files['js']) as $f) {
                $componentCssFiles[$class]['files'][] = $f;
            }
        }

        $out = array();
        foreach ($this->_getComponentClassesPackages() as $package=>$components) {
            $out[$package] = array();
            foreach ($components as $c) {
                $out[$package][$c] = $componentCssFiles[$c];
            }
        }
        echo json_encode($out);
        exit;
    }

    private $_componentClassesPackagesCache;
    private $_rootComponentClass;

    private function _getComponentClassesPackages()
    {
        if (isset($this->_componentClassesPackagesCache)) {
            return $this->_componentClassesPackagesCache;
        }

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
        if (isset($otherPackageClasses['Default'])) {
            $otherPackageClasses['Frontend'] = array_merge($otherPackageClasses['Frontend'], $otherPackageClasses['Default']);
            unset($otherPackageClasses['Default']);
        }

        $this->_componentClassesPackagesCache = $otherPackageClasses;
        return $this->_componentClassesPackagesCache;
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

    public function getComponentScssConfigAction()
    {
        $class = $this->_getParam('class');
        $ret = array(
            'config' => Kwc_Admin::getInstance($class)->getScssConfig(),
            'masterFiles' => Kwc_Admin::getInstance($class)->getScssConfigMasterFiles(),
        );
        echo json_encode($ret);
        exit;
    }
}

