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
            $processed = array();
            $ret = $this->_process($this->_rootComponentClass, $dependencyName, $processed);
            return $ret;
        } else {
            return null;
        }
    }

    private function _process($class, $dependencyName, &$processed)
    {
        $processed[] = $class;
        $assets = Kwc_Abstract::getSetting($class, 'assets');
        if ($dependencyName = 'ComponentsAdmin') {
            $aa = Kwc_Abstract::getSetting($class, 'assetsAdmin');
            $assets['files'] = array_merge($assets['files'], $aa['files']);
            $assets['dep'] = array_merge($assets['dep'], $aa['dep']);
        }

        $ret = array();
        foreach ($assets['dep'] as $i) {
            $ret[] = $i;
        }
        foreach ($assets['files'] as $i) {
            if (!is_object($i)) {
                $i = Kwf_Assets_Dependency_File::createDependency($i);
            }
            $ret[] = $i;
        }

        //alle css-dateien der vererbungshierache includieren
        $files = Kwc_Abstract::getSetting($class, 'componentFiles');
        $componentCssFiles = array();
        foreach (array_merge($files['css'], $files['printcss'], $files['scss'], $files['masterCss'], $files['masterScss']) as $f) {
            $componentCssFiles[] = $f;
        }
        //reverse damit css von weiter unten in der vererbungshierachie überschreibt
        $componentCssFiles = array_reverse($componentCssFiles);
        foreach ($componentCssFiles as $i) {
            $ret[] = Kwf_Assets_Dependency_File::createDependency($i);
        }

        $classes = Kwc_Abstract::getChildComponentClasses($class);
        $classes = array_merge($classes, Kwc_Abstract::getSetting($class, 'plugins'));
        foreach (Kwc_Abstract::getSetting($class, 'generators') as $g) {
            if (isset($g['plugins'])) {
                $classes = array_merge($classes, $g['plugins']);
            }
        }

        foreach ($classes as $i) {
            if ($i && !in_array($i, $processed)) {
                $ret = array_merge($ret, $this->_process($i, $dependencyName, $processed));
            }
        }

        return $ret;
    }
}
