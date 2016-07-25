<?php
class Kwf_Component_Renderer_Twig_TemplateLocator
{
    public static function getComponentTemplate($componentClass, $type = 'Component')
    {
        $cacheId = 'twig-cmp-'.$componentClass.'-'.$type;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId);
        if ($ret !== false) return $ret;

        $file = null;
        if ($type == 'Master'
            && Kwc_Abstract::hasSetting($componentClass, 'masterTemplate')
            && Kwc_Abstract::getSetting($componentClass, 'masterTemplate')
        ) {
            $relativeToIncludePath = Kwc_Abstract::getSetting($componentClass, 'masterTemplate');
        } else {
            static $namespaces;
            if (!isset($namespaces)) {
                $namespaces = include VENDOR_PATH.'/composer/autoload_namespaces.php';
            }

            $pos = strpos($componentClass, '_');
            $ns1 = substr($componentClass, 0, $pos+1);

            $pos = strpos($componentClass, '_', $pos+1);
            if ($pos !== false) {
                $ns2 = substr($componentClass, 0, $pos+1);
            } else {
                $ns2 = $componentClass;
            }

            $relativeToIncludePath = $componentClass;
            if (substr($relativeToIncludePath, -10) == '_Component') {
                $relativeToIncludePath = substr($relativeToIncludePath, 0, -10);
            }
            $relativeToIncludePath .= '_'.$type;
            $relativeToIncludePath = str_replace('_', '/', $relativeToIncludePath).'.twig';


            $dirs = false;
            if (isset($namespaces[$ns2])) {
                $dirs = $namespaces[$ns2];
            } else if (isset($namespaces[$ns1])) {
                $dirs = $namespaces[$ns1];
            }

            if ($dirs !== false) {
                if (count($dirs) == 1) {
                    $file = $dirs[0].'/'.$relativeToIncludePath;
                } else {
                    foreach ($dirs as $dir) {
                        if (file_exists($dir.'/'.$relativeToIncludePath)) {
                            $dir = rtrim($dir, '/');
                            if (VENDOR_PATH == '../vendor') { //hack for tests. proper solution would be not to change cwd into /tests
                                if ($dir == KWF_PATH) {
                                    $dir = '..';
                                }
                            }
                            $file = $dir.'/'.$relativeToIncludePath;
                            break;
                        }
                    }
                }
            }
        }

        if ($file) {
            $ret = $file;
        } else {
            $ret = null;
            foreach (explode(PATH_SEPARATOR, get_include_path()) as $ip) {
                $file = $ip.'/'.$relativeToIncludePath;
                if (file_exists(getcwd().'/'.$file)) {
                    $ret = getcwd().'/'.$file;
                    break;
                }
            }
            if (!$ret) {
                throw new Kwf_Exception("Can't find $type template for $componentClass $relativeToIncludePath");
            }
        }


        if (VENDOR_PATH == '../vendor') {
            $cwd = getcwd();
            $cwd = substr($cwd, 0, strrpos($cwd, '/'));
            if (substr($ret, 0, strlen($cwd)) != $cwd) {
                throw new Kwf_Exception("'$ret' not in cwd");
            }
            $ret = '../'.substr($ret, strlen($cwd)+1);
        } else {
            if (substr($ret, 0, strlen(getcwd())) != getcwd()) {
                throw new Kwf_Exception("'$ret' not in cwd");
            }
            $ret = substr($ret, strlen(getcwd())+1);
        }

        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
    }
}
