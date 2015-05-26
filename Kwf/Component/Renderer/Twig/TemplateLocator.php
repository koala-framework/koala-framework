<?php
class Kwf_Component_Renderer_Twig_TemplateLocator
{
    public static function getComponentTemplate($componentClass)
    {
        $cacheId = 'twig-cmp-'.$componentClass;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId);
        if ($ret !== false) return $ret;

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

        $file = null;

        $dirs = false;
        if (isset($namespaces[$ns2])) {
            $dirs = $namespaces[$ns2];
        } else if (isset($namespaces[$ns1])) {
            $dirs = $namespaces[$ns1];
        }
        if ($dirs !== false) {
            if (count($dirs) == 1) {
                $file = $dirs[0].'/'.str_replace('_', '/', $componentClass).'.twig';
            } else {
                foreach ($dirs as $dir) {
                    if (file_exists($dir.'/'.str_replace('_', '/', $componentClass).'.twig')) {
                        $dir = rtrim($dir, '/');
                        if (VENDOR_PATH == '../vendor') { //hack for tests. proper solution would be not to change cwd into /tests
                            if ($dir == KWF_PATH) {
                                $dir = '..';
                            }
                        }
                        $file = $dir.'/'.str_replace('_', '/', $componentClass).'.twig';
                        break;
                    }
                }
            }
        }

        if ($file) {
            $ret = $file;
        } else {
            $ret = null;
            foreach (explode(PATH_SEPARATOR, get_include_path()) as $ip) {
                $file = $ip.'/'.str_replace('_', '/', $componentClass).'.twig';
                if (file_exists(getcwd().'/'.$file)) {
                    $ret = getcwd().'/'.$file;
                    break;
                }
            }
            if (!$ret) {
                throw new Kwf_Exception("Can't find template $componentClass");
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
