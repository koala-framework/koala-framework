<?php
class Kwf_Assets_Provider_Components extends Kwf_Assets_Provider_Abstract
{
    private $_rootComponentClass;

    public function __construct($rootComponentClass)
    {
        $this->_rootComponentClass = $rootComponentClass;
    }

    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'Components') {
            $ret = array();
            $files = Kwf_Component_Abstract_Admin::getComponentFiles($this->_rootComponentClass, array(
                'css' => array('filename'=>'Web', 'ext'=>'css', 'returnClass'=>false, 'multiple'=>true),
                'printcss' => array('filename'=>'Web', 'ext'=>'printcss', 'returnClass'=>false, 'multiple'=>true),
                'scss' => array('filename'=>'Web', 'ext'=>'scss', 'returnClass'=>false, 'multiple'=>true),
            ));
            foreach ($files as $i) {
                foreach ($i as $j) {
                    $jj = Kwf_Assets_Dependency_File::getPathWithTypeByFileName($j);
                    if (!$jj) {
                        throw new Kwf_Exception("Can't find path type for '$j'");
                    }
                    $ret[] = Kwf_Assets_Dependency_File::createDependency($jj, $this->_providerList);
                }
            }

            $componentClasses = $this->_getRecursiveChildClasses($this->_rootComponentClass);

            $addedFiles = array();

            foreach ($componentClasses as $class) {

                $ret = array_merge($ret, $this->_getComponentSettingDependencies($class, 'assets', $addedFiles));

                //alle css-dateien der vererbungshierache includieren
                $files = Kwc_Abstract::getSetting($class, 'componentFiles');
                $componentCssFiles = array();
                foreach (array_merge($files['css'], $files['printcss'], $files['scss'], $files['js'], $files['masterCss'], $files['masterScss']) as $f) {
                    $componentCssFiles[] = $f;
                }
                //reverse damit css von weiter unten in der vererbungshierachie überschreibt
                $componentCssFiles = array_reverse($componentCssFiles);
                foreach ($componentCssFiles as $i) {
                    if (!in_array($i, $addedFiles)) {
                        $addedFiles[] = $i;
                        $i = Kwf_Assets_Dependency_File::getPathWithTypeByFileName($i);
                        $ret[] = Kwf_Assets_Dependency_File::createDependency($i, $this->_providerList);
                    }
                }
            }
            return new Kwf_Assets_Dependency_Dependencies($ret, $dependencyName);

        } else if ($dependencyName == 'ComponentsAdmin') {
            $ret = array();
            $addedFiles = array();
            $componentClasses = $this->_getRecursiveChildClasses($this->_rootComponentClass);
            foreach ($componentClasses as $class) {
                $ret = array_merge($ret, $this->_getComponentSettingDependencies($class, 'assetsAdmin', $addedFiles));
            }
            return new Kwf_Assets_Dependency_Dependencies($ret, $dependencyName);
        }
        return null;
    }

    private function _getComponentSettingDependencies($class, $setting, &$addedFiles)
    {
        $ret = array();
        $assets = Kwc_Abstract::getSetting($class, $setting);
        foreach ($assets['dep'] as $i) {
            $d = $this->_providerList->findDependency(trim($i));
            if (!$d) {
                throw new Kwf_Exception("Can't find dependency '$i'");
            }
            $ret[] = $d;
        }
        foreach ($assets['files'] as $i) {
            if (!in_array($i, $addedFiles)) {
                $addedFiles[] = $i;
                if (!is_object($i)) {
                    $i = Kwf_Assets_Dependency_File::createDependency($i, $this->_providerList);
                }
                $ret[] = $i;
            }
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
