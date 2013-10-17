<?php
/**
 * Internal Helper class for reading component settings
 *
 * @internal
 */
class Kwf_Component_Settings
{
    private static $_settings = null;
    public static $_rebuildingSettings = false;
    private static $_cacheSettings = array();

    public static function hasSettings($class)
    {
        $cacheId = 'hasSettings-'.$class;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        //& für performance
        $s =& self::_getSettingsCached();
        $ret = isset($s[$class]);
        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
    }

    public static function hasSetting($class, $setting)
    {
        if (self::$_rebuildingSettings) {
            //um endlosschleife in settingsCache zu verhindern

            self::_verifyComponentClass($class);
            $settings = self::_loadCacheSettings($class);
            return isset($settings[$setting]);
        }

        $cacheId = 'has-'.$class.'-'.$setting;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        //& für performance
        $s =& self::_getSettingsCached();
        if (!isset($s[$class])) {
            throw new Kwf_Exception("No Settings for component '$class' found; it is probably not in allComponentClasses. Requested setting: $setting");
        }
        $ret = array_key_exists($setting, $s[$class]);
        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
    }

    private static function _verifyComponentClass($class)
    {
        $c = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
        $file = 'components/'.str_replace('_', '/', $c).'.yml';
        if (substr($class, 0, 4) != 'Kwc_' && file_exists($file)) {
            if (file_exists('components/'.str_replace('_', '/', $c).'.php')) {
                throw new Kwf_Exception("both yml and php exist for '$c'");
            }
            $classFile = 'cache/generated/'.str_replace('_', '/', $c).'.php';
            if (!file_exists($classFile)) {
                $input = file_get_contents($file);
                require_once Kwf_Config::getValue('externLibraryPath.sfYaml').'/sfYamlParser.php';
                $yaml = new sfYamlParser();
                try {
                    $settings = $yaml->parse($input);
                } catch (Exception $e) {
                    throw new Kwf_Exception(sprintf('Unable to parse %s: %s', $file, $e->getMessage()));
                }
                if (!isset($settings['base'])) {
                    throw new Kwf_Exception("'base' setting is required in '$file'");
                }
                if (!class_exists($settings['base'])) {
                    throw new Kwf_Exception("'$file' base class '$settings[base]' does not exist");
                }
                $code = "<?php\nclass $c extends $settings[base]\n{\n";
                $code .= "    public static function _getYamlConfigFile() { return '$file'; }\n";
                $code .= "}\n";
                $dir = substr($classFile, 0, strrpos($classFile, '/'));
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                file_put_contents($classFile, $code);
                if (!class_exists($c)) {
                    throw new Kwf_Exception("just generated class still does not exist");
                }
            }
        } else {
            if (!class_exists($c)) {
                throw new Kwf_Exception("Invalid component '$c'");
            }
        }
    }

    private static function _processYamlSettings(&$settings)
    {
        foreach ($settings as $k=>$i) {
            if (is_string($i) && preg_match('#^\\s*(trl|trlKwf)\\(\'(.*)\'\\)\s*$#', $i, $m)) {
                $fn = $m[1]; //trl or trlKwf
                $settings[$k] = Kwf_Trl::getInstance()->$fn($m[2], array());
            } else if (is_string($i) && preg_match('#^\\.\'(.*)\'$#', $i, $m)) {
                if (isset($settings[$k])) {
                    $settings[$k] = $settings[$k] . $m[1];
                } else {
                    $settings[$k] = $m[1];
                }
            }
            if (is_array($i)) {
                self::_processYamlSettings($settings[$k]);
            }
        }
    }

    private static function _mergeSettings(&$settings, $mergeSettings)
    {
        foreach ($mergeSettings as $k=>$i) {
            if ((string)$k=='_merge') {
                //ignore, not a setting; only for controling merging
                continue;
            }
            if (is_array($i)) {
                /*
                if (isset($i['_merge']) && $i['_merge'] == 'parentComponent') {
                    //keep $settings[$k]
                    if (!isset($settings[$k])) $settings[$k] = array();
                } else {
                    $settings[$k] = array(); //no merge; empty parent settings
                }
                */
                if (isset($i['_merge']) && $i['_merge'] == 'reset') {
                    $settings[$k] = array(); //no merge; empty parent settings
                } else if (!isset($settings[$k])) {
                    $settings[$k] = array();
                } else {
                    if (!isset($settings[$k])) $settings[$k] = array();
                }
                self::_mergeSettings($settings[$k], $i);
            } else {
                $settings[$k] = $i;
            }
        }
    }

    private static function _loadCacheSettings($class)
    {
        $fullT = microtime(true);
        $c = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
        $param = strpos($class, '.') ? substr($class, strpos($class, '.')+1) : null;
        if (!isset(self::$_cacheSettings[$c][$param])) {
            $settings = call_user_func(array($c, 'getSettings'), $param);
            if (method_exists($c, '_getYamlConfigFile')) {
                $file = call_user_func(array($c, '_getYamlConfigFile'));
                require_once Kwf_Config::getValue('externLibraryPath.sfYaml').'/sfYamlParser.php';
                $input = file_get_contents($file);
                $yaml = new sfYamlParser();
                try {
                    $mergeSettings = $yaml->parse($input);
                } catch (Exception $e) {
                    throw new Kwf_Exception(sprintf('Unable to parse %s: %s', $file, $e->getMessage()));
                }
                if (isset($mergeSettings['settings'])) {
                    self::_processYamlSettings($mergeSettings['settings']);
                    self::_mergeSettings($settings, $mergeSettings['settings']);
                }
                if (isset($mergeSettings['childSettings'])) {
                    if (isset($settings['childSettings'])) {
                        self::_processYamlSettings($mergeSettings['childSettings']);
                        self::_mergeSettings($settings['childSettings'], $mergeSettings['childSettings']);
                    } else {
                        $settings['childSettings'] = $mergeSettings['childSettings'];
                    }
                }
            }
            if (substr($param, 0, 2)=='cs') { //child settings
                $childSettingsComponentClass = substr($param, 2, strpos($param, '>')-2);
                $childSettingsKey = substr($param, strpos($param, '>')+1);
                $childSettingsKey = str_replace('>', '.', $childSettingsKey);
                $cs = self::getSetting($childSettingsComponentClass, 'childSettings');
                if (isset($cs[$childSettingsKey])) {
                    self::_mergeSettings($settings, $cs[$childSettingsKey]);
                }
            }
            if (isset($settings['componentIcon']) && is_string($settings['componentIcon'])) {
                $settings['componentIcon'] = new Kwf_Asset($settings['componentIcon']);
            }
            self::$_cacheSettings[$c][$param] = $settings;
        }
        return self::$_cacheSettings[$c][$param];
    }

    public function _getSettingsIncludingPreComputed($c)
    {
        $settings = self::_loadCacheSettings($c);

        //*** load templates + componentFiles
        //vorladen fuer Kwf_Component_Abstract_Admin::getComponentFile
        $settings['componentFiles'] = Kwf_Component_Abstract::getSetting($c, 'componentFiles');

        //*** parentClasses
        $settings['parentClasses'] = Kwf_Component_Abstract::getSetting($c, 'parentClasses');

        //*** parentFilePaths
        $settings['parentFilePaths'] = Kwf_Component_Abstract::getSetting($c, 'parentFilePaths');

        //*** processedCssClass
        $settings['processedCssClass'] = '';
        if (isset($settings['cssClass'])) {
            $settings['processedCssClass'] .= $settings['cssClass'].' ';
        }
        $cssClass = array(Kwf_Component_Abstract::formatCssClass($c));
        $dirs = explode(PATH_SEPARATOR, get_include_path());
        foreach ($settings['parentClasses'] as $i) {
            if ($i == $c) continue;
            $file = str_replace('_', '/', $i);
            if (substr($file, -10) != '/Component') {
                $file .= '/Component';
            }
            foreach ($dirs as $dir) {
                if (is_file($dir.'/'.$file.'.css') || is_file($dir.'/'.$file.'.scss') || is_file($dir.'/'.$file.'.printcss') || is_file($dir.'/'.$file.'.js')) {
                    $cssClass[] = Kwf_Component_Abstract::formatCssClass($i);
                    break;
                }
            }
        }
        $settings['processedCssClass'] .= implode(' ', array_reverse($cssClass));
        $settings['processedCssClass'] = trim($settings['processedCssClass']);

        //*** generators
        $settings['generators'] = Kwf_Component_Abstract::getSetting($c, 'generators');

        return $settings;
    }

    private static function _addChildSettingsParam($componentClass, $csParam)
    {
        if (substr($componentClass, -strlen($csParam)-3) == '.cs'.$csParam) return $componentClass;
        if (preg_match('#^[a-z0-9_]+.cs[a-z0-9_]+>#i', $componentClass)) {
            throw new Kwf_Exception("can't add another childSettings parameter '$csParam' to '$componentClass'");
        }
        return $componentClass . '.cs' . $csParam;
    }

    public static function getSetting($class, $setting)
    {
        if (self::$_rebuildingSettings) {
            //um endlosschleife in settingsCache zu verhindern

            self::_verifyComponentClass($class);
            if ($setting == 'parentClasses') {
                $p = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
                $ret = array();
                do {
                    $ret[] = $p;
                } while ($p = get_parent_class($p));
            } else if ($setting == 'parentFilePaths') {
                //value = klasse, key=pfad
                $ret = array();
                foreach (self::getSetting($class, 'parentClasses') as $c) {
                    if (method_exists($c, '_getYamlConfigFile')) {
                        $file = call_user_func(array($c, '_getYamlConfigFile'));
                    } else {
                        $file = str_replace('_', DIRECTORY_SEPARATOR, $c) . '.php';
                    }
                    $dirs = explode(PATH_SEPARATOR, get_include_path());
                    foreach ($dirs as $dir) {
                        if ($dir == '.') $dir = getcwd();
                        if (!preg_match('#^(/|\w\:\\\\)#i', $dir)) {
                            //relative path, make it absolute
                            $dir = getcwd().'/'.$dir;
                        }
                        $path = $dir . '/' . $file;
                        if (is_file($path)) {
                            if (substr($path, -14) == DIRECTORY_SEPARATOR.'Component.php' || substr($path, -14) == DIRECTORY_SEPARATOR.'Component.yml') {
                                $ret[substr($path, 0, -14)] = substr($c, 0, -10);
                            } else {
                                $ret[substr($path, 0, -4)] = $c; //nur .php
                            }
                            break;
                        }
                    }
                }
            } else if ($setting == 'componentFiles') {
                $ret = Kwf_Component_Abstract_Admin::getComponentFiles($class, array(
                    'Master.tpl' => array('filename'=>'Master', 'ext'=>'tpl', 'returnClass'=>false),
                    'Component.tpl' => array('filename'=>'Component', 'ext'=>'tpl', 'returnClass'=>false),
                    'Partial.tpl' => array('filename'=>'Partial', 'ext'=>'tpl', 'returnClass'=>false),
                    'Admin' => array('filename'=>'Admin', 'ext'=>'php', 'returnClass'=>true),
                    'Controller' => array('filename'=>'Controller', 'ext'=>'php', 'returnClass'=>true),
                    'FrontendForm' => array('filename'=>'FrontendForm', 'ext'=>'php', 'returnClass'=>true),
                    'Form' => array('filename'=>'Form', 'ext'=>'php', 'returnClass'=>true),

                    //verwendet bei dependencies
                    'css' => array('filename'=>'Component', 'ext'=>'css', 'returnClass'=>false, 'multiple'=>true),
                    'printcss' => array('filename'=>'Component', 'ext'=>'printcss', 'returnClass'=>false, 'multiple'=>true),
                    'scss' => array('filename'=>'Component', 'ext'=>'scss', 'returnClass'=>false, 'multiple'=>true),
                    'masterCss' => array('filename'=>'Master', 'ext'=>'css', 'returnClass'=>false, 'multiple'=>true),
                    'masterScss' => array('filename'=>'Master', 'ext'=>'scss', 'returnClass'=>false, 'multiple'=>true),
                ));
            } else {

                $settings = self::_loadCacheSettings($class);

                if (!array_key_exists($setting, $settings)) {
                    throw new Kwf_Exception("Couldn't find required setting '$setting' for $class.");
                }
                $ret = $settings[$setting];
                if ($setting == 'generators') {
                    if (isset($settings['childSettings'])) {
                        $processed = array();
                        foreach ($settings['childSettings'] as $csKeys=>$childSettings) {
                            $csKeys = explode('.', $csKeys);
                            $csKey = explode('_', $csKeys[0]); //just the first
                            if (!isset($ret[$csKey[0]])) {
                                throw new Kwf_Exception("invalid childSetting; generator '$csKey[0]' does not exist");
                            }
                            if (is_array($ret[$csKey[0]]['component'])) {
                                if (!isset($csKey[1])) {
                                    throw new Kwf_Exception("invalid childSetting; component key required");
                                }
                                if (!isset($ret[$csKey[0]]['component'][$csKey[1]])) {
                                    throw new Kwf_Exception("invalid childSetting; component '$csKey[1]' does not exist for generator '$csKey[0]'");
                                }
                                $ret[$csKey[0]]['component'][$csKey[1]] = self::_addChildSettingsParam($ret[$csKey[0]]['component'][$csKey[1]], $class.'>'.$csKey[0].'_'.$csKey[1]);
                            } else {
                                $ret[$csKey[0]]['component'] = self::_addChildSettingsParam($ret[$csKey[0]]['component'], $class.'>'.$csKey[0]);
                            }
                        }
                    }

                    $param = strpos($class, '.') ? substr($class, strpos($class, '.')+1) : null;
                    if ($param && substr($param, 0, 2)=='cs') {
                        $childSettingsComponentClass = substr($param, 2, strpos($param, '>')-2);
                        $childSettingsKey = str_replace('>', '.', substr($param, strpos($param, '>')+1));
                        $allChildSettings = Kwc_Abstract::getSetting($childSettingsComponentClass, 'childSettings');
                        foreach ($allChildSettings as $csKeys=>$childSettings) {
                            if (substr($csKeys, 0, strlen($childSettingsKey)) != $childSettingsKey) continue;
                            if ($csKeys == $childSettingsKey) continue;
                            $csKeys = explode('.', substr($csKeys, strlen($childSettingsKey)+1));
                            $csKey = explode('_', $csKeys[0]); //just the first
                            if (!isset($ret[$csKey[0]])) {
                                throw new Kwf_Exception("invalid childSetting; generator '$csKey[0]' does not exist for '$class'");
                            }
                            if (is_array($ret[$csKey[0]]['component'])) {
                                if (!isset($csKey[1])) {
                                    throw new Kwf_Exception("invalid childSetting; component key required");
                                }
                                if (!isset($ret[$csKey[0]]['component'][$csKey[1]])) {
                                    throw new Kwf_Exception("invalid childSetting; component '$csKey[1]' does not exist for generator '$csKey[0]'");
                                }
                                $ret[$csKey[0]]['component'][$csKey[1]] = self::_addChildSettingsParam($ret[$csKey[0]]['component'][$csKey[1]], substr($param, 2).'>'.$csKey[0].'_'.$csKey[1]);
                            } else {
                                $ret[$csKey[0]]['component'] = self::_addChildSettingsParam($ret[$csKey[0]]['component'], substr($param, 2).'>'.$csKey[0]);
                            }
                        }
                    }

                    $retModified = array();
                    foreach ($ret as $k=>$g) {
                        if (is_array($g['component'])) {
                            foreach ($g['component'] as $l=>$cc) {
                                if (!$cc) continue;
                                $cc = strpos($cc, '.') ? substr($cc, 0, strpos($cc, '.')) : $cc;
                                $vars = get_class_vars($cc);
                                if (isset($vars['needsParentComponentClass']) && $vars['needsParentComponentClass']) {
                                    $g['component'][$l] .= '.'.$class;
                                }
                            }
                        } else {
                            if (!$g['component']) continue;
                            $cc = $g['component'];
                            $cc = strpos($cc, '.') ? substr($cc, 0, strpos($cc, '.')) : $cc;
                            $vars = get_class_vars($cc);
                            if (isset($vars['needsParentComponentClass']) && $vars['needsParentComponentClass']) {
                                $g['component'] .= '.'.$class;
                            }
                        }
                        $retModified[$k] = $g;
                    }
                    $ret = $retModified;
                }
            }
            return $ret;
        }

        $cacheId = 'cs-'.$class.'-'.$setting;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        if (!self::$_settings) self::_getSettingsCached();
        try {
            $ret = self::$_settings[$class][$setting];
        } catch (ErrorException $e) {
            //diese checks im nachhinein machen damit sie nicht immer gemacht werden (diese fkt wird am meisten von allen aufgerufen)
            //und hier dann versuchen eine bessere exception msg zu erstellen
            if (!is_string($class)) {
                throw new Kwf_Exception("Invalid component '$class'");
            } else if (!isset(self::$_settings[$class])) {
                throw new Kwf_Exception("No Settings for component '$class' found; it is probably not in allComponentClasses.");
            } else if (!array_key_exists($setting, self::$_settings[$class])) {
                // man könnte hier isset() machen, nur wenn das setting NULL ist, gibt es false zurück... scheis PHP :)
                throw new Kwf_Exception("Setting '$setting' does not exist for Component '$class'");
            } else {
                throw $e;
            }
        }
        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
    }

    public static function getSettingMtime()
    {
        if (!Kwf_Config::getValue('kwc.rootComponent')) return 0;

        $cacheId = 'settingsMtime';
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $s =& self::_getSettingsCached();
        $ret = $s['mtime'];
        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
    }

    //wenn root geändert wird muss der cache hier gelöscht werden können
    public static function resetSettingsCache()
    {
        self::$_settings = null;
        self::$_rebuildingSettings = false;
        self::$_cacheSettings = array();
    }

    public static function getAllSettingsCache()
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new Kwf_Cache_Core(array(
                'checkComponentSettings' => false,
                'lifetime' => null,
                'automatic_serialization' => true,
                'automatic_cleaning_factor' => 0,
            ));
            $cache->setBackend(new Zend_Cache_Backend_File(array(
                'cache_dir' => 'cache/component',
                'cache_file_umask' => 0666,
                'hashed_directory_umask' => 0777,
            )));
        }
        return $cache;
    }

    public static function &_getSettingsCached()
    {
        self::$_cacheSettings = array();
        if (!self::$_settings) {
            $cacheId = 'componentSettings_'.str_replace('.', '_', Kwf_Component_Data_Root::getComponentClass());
            self::$_settings = self::getAllSettingsCache()->load($cacheId);
            if (!self::$_settings) {
                $fullT = microtime(true);

                self::$_rebuildingSettings = true;
                self::$_settings = array();
                self::$_settings['mtimeFiles'] = array();
                $incPaths = explode(PATH_SEPARATOR, get_include_path());

                $t = microtime(true);
                $classes = self::getComponentClasses();
                foreach ($classes as $c) {
                    self::$_settings[$c] = self::_getSettingsIncludingPreComputed($c);
                }
                self::$_rebuildingSettings = false;

                foreach (self::getComponentClasses() as $c) {
                    $realCls = strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c;
                    try {
                        call_user_func(array($realCls, 'validateSettings'), self::$_settings[$c], $c);
                    } catch (Kwf_Exception $e) {
                        throw new Kwf_Exception("$c: ".$e->getMessage());
                    }
                }

                self::getAllSettingsCache()->save(self::$_settings, $cacheId);
            }
        }
        return self::$_settings;
    }
    public static function getComponentClasses()
    {
        $root = Kwf_Component_Data_Root::getComponentClass();
        if (!$root) return array();
        if (!self::$_rebuildingSettings) {
            $cacheId = 'componentClasses-'.Kwf_Component_Data_Root::getComponentClass();
            $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
            if ($success) {
                return $ret;
            }
            $s =& self::_getSettingsCached();
            $ret = array_keys($s);
            unset($ret[array_search('mtime', $ret)]);
            unset($ret[array_search('mtimeFiles', $ret)]);
            $ret = array_values($ret);
            Kwf_Cache_SimpleStatic::add($cacheId, $ret);
            return $ret;
        }
        $componentClasses = array($root);
        self::_getChildComponentClasses($componentClasses, $root);
        return $componentClasses;
    }

    static public function getFlag($class, $flag)
    {
        $cacheId = 'flag-'.$class.'-'.$flag;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $flags = self::getSetting($class, 'flags');
        if (!isset($flags[$flag])) {
            $ret = false;
        } else {
            $ret = $flags[$flag];
        }
        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
    }

    private static function _getChildComponentClasses(&$componentClasses, $class)
    {
        $tFull = microtime(true);
        $classes = array();
        foreach (Kwc_Abstract::getSetting($class, 'generators') as $generator) {
            if (is_array($generator['component'])) {
                $classes = array_merge($classes, $generator['component']);
            } else {
                $classes[] = $generator['component'];
            }
            if (isset($generator['plugins'])) {
                $classes = array_merge($classes, $generator['plugins']);
            }
        }
        $plugins = Kwc_Abstract::getSetting($class, 'plugins');
        if (is_array($plugins)) {
            $classes = array_merge($classes, $plugins);
        }
        if (Kwc_Abstract::getFlag($class, 'hasAlternativeComponent')) {
            $c = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
            $alternativeComponents = call_user_func(array($c, 'getAlternativeComponents'), $class);
            $classes = array_merge($classes, $alternativeComponents);
        }
        foreach ($classes as $c) {
            if ($c&& !in_array($c, $componentClasses)) {
                self::_verifyComponentClass($c);
                if (!class_exists(strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c)) {
                    throw new Kwf_Exception("Component Class '$c' does not exist, used in '$class'");
                }
                $componentClasses[] = $c;
                self::_getChildComponentClasses($componentClasses, $c);
            }
        }
    }
}
