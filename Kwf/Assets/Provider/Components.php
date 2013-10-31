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
        if ($dependencyName == 'Components' || $dependencyName == 'ComponentsAdmin') {

            $ret = array();

            if ($dependencyName == 'Components') {
                $files = Kwf_Component_Abstract_Admin::getComponentFiles($this->_rootComponentClass, array(
                    'css' => array('filename'=>'Web', 'ext'=>'css', 'returnClass'=>false, 'multiple'=>true),
                    'printcss' => array('filename'=>'Web', 'ext'=>'printcss', 'returnClass'=>false, 'multiple'=>true),
                    'scss' => array('filename'=>'Web', 'ext'=>'scss', 'returnClass'=>false, 'multiple'=>true),
                ));
                foreach ($files as $i) {
                    foreach ($i as $j) {
                        $ret[] = Kwf_Assets_Dependency_File::createDependency($j);
                    }
                }
            }

            $componentClasses = $this->_getRecursiveChildClasses($this->_rootComponentClass);

            $addedFiles = array();
            $addedDep = array();

            foreach ($componentClasses as $class) {

                if ($dependencyName == 'ComponentsAdmin') {
                    $assets = Kwc_Abstract::getSetting($class, 'assetsAdmin');
                } else {
                    $assets = Kwc_Abstract::getSetting($class, 'assets');
                }
                foreach ($assets['dep'] as $i) {
                    if (!in_array($i, $addedDep)) {
                        $addedDep[] = $i;
                        $d = $this->_providerList->findDependency(trim($i));
                        if (!$d) {
                            throw new Kwf_Exception("Can't find dependency '$i'");
                        }
                        $ret[] = $d;
                    }
                }
                foreach ($assets['files'] as $i) {
                    if (!in_array($i, $addedFiles)) {
                        $addedFiles[] = $i;
                        if (!is_object($i)) {
                            $i = Kwf_Assets_Dependency_File::createDependency($i);
                        }
                        $ret[] = $i;
                    }
                }

                if ($dependencyName == 'Components') {
                    //alle css-dateien der vererbungshierache includieren
                    $files = Kwc_Abstract::getSetting($class, 'componentFiles');
                    $componentCssFiles = array();
                    foreach (array_merge($files['css'], $files['printcss'], $files['scss'], $files['masterCss'], $files['masterScss']) as $f) {
                        $componentCssFiles[] = $f;
                    }
                    //reverse damit css von weiter unten in der vererbungshierachie überschreibt
                    $componentCssFiles = array_reverse($componentCssFiles);
                    foreach ($componentCssFiles as $i) {
                        if (!in_array($i, $addedFiles)) {
                            $addedFiles[] = $i;
                            $ret[] = Kwf_Assets_Dependency_File::createDependency($i);
                        }
                    }
                }
            }
            return new Kwf_Assets_Dependency_Dependencies($ret, $dependencyName);
        } else {
            return null;
        }
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
