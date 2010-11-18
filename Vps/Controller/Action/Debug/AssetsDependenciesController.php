<?php
class Vps_Controller_Action_Debug_AssetsDependenciesController extends Vps_Controller_Action
{
    private $_config;
    private $_processedDependencies = array();
    private $_processedComponents = array();
    private $_stack = array();

    public function indexAction()
    {
        $this->_config = Vps_Registry::get('config');
        $assetsType = 'Frontend';
        $rootComponent = Vps_Component_Data_Root::getComponentClass();
        foreach ($this->_config->assets->$assetsType as $d=>$v) {
            if ($v) {
                $this->_processDependency($assetsType, $d, $rootComponent);
            }
        }
        exit;
    }

    private function _getDependenciesConfig($assetsType)
    {
        $ret = new Zend_Config_Ini(VPS_PATH.'/config.ini', 'dependencies',
                                            array('allowModifications'=>true));
        $ret->merge(new Zend_Config_Ini('application/config.ini', 'dependencies'));
        return $ret;
    }

    private function _processDependency($assetsType, $dependency, $rootComponent)
    {
        if (in_array($assetsType.$dependency, $this->_processedDependencies)) return;
        array_push($this->_stack, $dependency);
        $this->_processedDependencies[] = $assetsType.$dependency;
        if ($dependency == 'Components' || $dependency == 'ComponentsAdmin') {
            $this->_processComponentDependency($assetsType, $rootComponent, $rootComponent, $dependency == 'ComponentsAdmin');
            array_pop($this->_stack);
            return;
        }
        if (!isset($this->_getDependenciesConfig($assetsType)->$dependency)) {
            throw new Vps_Exception("Can't resolve dependency '$dependency'");
        }
        $deps = $this->_getDependenciesConfig($assetsType)->$dependency;

        if (isset($deps->dep)) {
            foreach ($deps->dep as $d) {
                $this->_processDependency($assetsType, $d, $rootComponent);
            }
        }

        if (isset($deps->files)) {
            foreach ($deps->files as $file) {
                $this->_processDependencyFile($assetsType, $file, $rootComponent);
            }
        }
        array_pop($this->_stack);
        return;
    }

    private function _processComponentDependency($assetsType, $class, $rootComponent, $includeAdminAssets)
    {
        if (in_array($assetsType.$class.$includeAdminAssets, $this->_processedComponents)) return;

        array_push($this->_stack, $class);
        $assets = Vpc_Abstract::getSetting($class, 'assets');
        $assetsAdmin = array();
        if ($includeAdminAssets) {
            $assetsAdmin = Vpc_Abstract::getSetting($class, 'assetsAdmin');
        }
        $this->_processedComponents[] = $assetsType.$class.$includeAdminAssets;
        if (isset($assets['dep'])) {
            foreach ($assets['dep'] as $dep) {
                $this->_processDependency($assetsType, $dep, $rootComponent);
            }
        }
        if (isset($assetsAdmin['dep'])) {
            foreach ($assetsAdmin['dep'] as $dep) {
                $this->_processDependency($assetsType, $dep, $rootComponent);
            }
        }
        if (isset($assets['files'])) {
            foreach ($assets['files'] as $file) {
                $this->_processDependencyFile($assetsType, $file, $rootComponent);
            }
        }
        if (isset($assetsAdmin['files'])) {
            foreach ($assetsAdmin['files'] as $file) {
                $this->_processDependencyFile($assetsType, $file, $rootComponent);
            }
        }

        //alle css-dateien der vererbungshierache includieren
        $componentCssFiles = array();

        foreach (Vpc_Abstract::getParentClasses($class) as $c) {
            $curClass = $c;
            if (substr($curClass, -10) == '_Component') {
                $curClass = substr($curClass, 0, -10);
            }
            $curClass =  $curClass . '_Component';
            $file = str_replace('_', DIRECTORY_SEPARATOR, $curClass);
            foreach ($this->_config->path as $type=>$dir) {
                if ($dir == '.') $dir = getcwd();
                if (is_file($dir . '/' . $file.'.css')) {
                    $f = $type . '/' . $file.'.css';
                    $this->_processDependencyFile($assetsType, $f);
                }
                if (is_file($dir . '/' . $file.'.printcss')) {
                    $f = $type . '/' . $file.'.printcss';
                    $this->_processDependencyFile($assetsType, $f);
                }
            }
        }

        $classes = Vpc_Abstract::getChildComponentClasses($class);
        $classes = array_merge($classes, Vpc_Abstract::getSetting($class, 'plugins'));
        foreach ($classes as $class) {
            if ($class) {
                $this->_processComponentDependency($assetsType, $class, $rootComponent, $includeAdminAssets);
            }
        }
        array_pop($this->_stack);
    }

    private function _processDependencyFile($assetsType, $file)
    {
        if (substr($file, -3) == '.js') {
            echo "<h1>$file</h1>\n";
            $l = new Vps_Assets_Loader();
            $c = $l->getFileContents("web-".$file);
            echo round(strlen($l->pack($c['contents'], 'js'))/1024)."kB";
            echo "<ul>";
            foreach ($this->_stack as $s) {
                echo "<li>$s</li>";
            }
            echo "</ul>\n";
        }
    }
}
