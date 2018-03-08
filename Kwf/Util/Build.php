<?php
class Kwf_Util_Build
{
    /**
     * @return Kwf_Util_Build
     */
    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            $c = Kwf_Registry::get('config')->buildClass;
            if (!$c) $c = 'Kwf_Util_Build';
            $i = new $c();
        }
        return $i;
    }

    public function getTypes()
    {
        $types = array();
        if (Kwf_Component_Data_Root::getComponentClass()) {
            $types[] = new Kwf_Util_Build_Types_ComponentSettings();
        }
        $types[] = new Kwf_Util_Build_Types_Trl();
        $types[] = new Kwf_Util_Build_Types_Events();
        $types[] = new Kwf_Util_Build_Types_Assets();

        $files = array('composer.json');
        $files = array_merge($files, glob('vendor/*/*/composer.json'));
        foreach ($files as $file) {
            $composerJson = json_decode(file_get_contents($file), true);
            if (isset($composerJson['extra']['koala-framework-build-step'])) {
                $steps = $composerJson['extra']['koala-framework-build-step'];
                if (!is_array($steps)) $steps = array($steps);
                foreach ($steps as $type) {
                    $types[] = new $type();
                }
            }
        }

        return $types;
    }

    public function getTypeNames()
    {
        $ret = array();
        foreach ($this->getTypes() as $t) {
            $ret[] = $t->getTypeName();
        }
        return $ret;
    }

    /**
     * @param array possible options: types(=all), output(=false), excludeTypes
     */
    public final function build(array $options)
    {
        $typeNames = $options['types'];
        $output = isset($options['output']) ? $options['output'] : false;
        $excludeTypes = isset($options['excludeTypes']) ? $options['excludeTypes'] : array();

        Kwf_Cache_SimpleStatic::disableFileCache();
        Kwf_Events_ModelObserver::getInstance()->disable();

        Kwf_Util_MemoryLimit::set(1024*2);
        Kwf_Registry::set('db', false);

        if ($typeNames == 'all') {
            $types = $this->getTypes();
        } else {
            if (!is_array($typeNames)) {
                $typeNames = explode(',', $typeNames);
            }
            $types = array();
            foreach ($this->getTypes() as $t) {
                if (in_array($t->getTypeName(), $typeNames)) {
                    $types[] = $t;
                }
            }
        }
        if (is_string($excludeTypes)) $excludeTypes = explode(',', $excludeTypes);
        foreach ($types as $k=>$i) {
            if (in_array($i->getTypeName(), $excludeTypes)) unset($types[$k]);
        }

        $maxTypeNameLength = 0;
        $countSteps = 0;
        foreach ($types as $type) {
            $type->setVerbosity($output ? Kwf_Util_Build_Types_Abstract::VERBOSE : Kwf_Util_Build_Types_Abstract::SILENT);
            $maxTypeNameLength = max($maxTypeNameLength, strlen($type->getTypeName()));
            $countSteps++;
        }

        $progress = null;
        if (isset($options['progressAdapter'])) {
            $progress = new Zend_ProgressBar($options['progressAdapter'], 0, $countSteps);
        }

        if (!file_exists('build')) {
            mkdir('build');
        }

        $currentStep = 0;
        foreach ($types as $type) {
            $currentStep++;
            if ($progress) $progress->next(1, "building ".$type->getTypeName());
            if ($output) {
                echo "[".str_repeat(' ', 2-strlen($currentStep))."$currentStep/$countSteps] ";
                echo "building ".$type->getTypeName()."...".str_repeat('.', $maxTypeNameLength - strlen($type->getTypeName()))." ";
            }
            $t = microtime(true);
            $type->build($options);
            if ($output) {
                if ($type->getSuccess()) {
                    echo "\033[00;32mOK\033[00m";
                } else {
                    echo " [\033[01;31mERROR\033[00m]";
                    return false;
                }
                echo " (".round((microtime(true)-$t)*1000)."ms)";
                echo "\n";
            }
        }

        Kwf_Events_ModelObserver::getInstance()->enable();
        return $types;
    }
}
