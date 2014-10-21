<?php
class Kwf_Component_Renderer_Twig_Helper
{
    private $_renderer;
    public function __construct(Kwf_Component_Renderer_Abstract $renderer)
    {
        $this->_renderer = $renderer;
    }

    public function component(Kwf_Component_Data $component = null)
    {
        return new Twig_Markup($this->_renderer->getHelper('component')->component($component), 'utf-8');
    }

    public function componentLink(Kwf_Component_Data $component, $text = null, $config = array())
    {
        return new Twig_Markup($this->_renderer->getHelper('componentLink')->componentLink($component, $text, $config), 'utf-8');
    }

    public function partials($component, $params = array())
    {
        return new Twig_Markup($this->_renderer->getHelper('partials')->partials($component, $params), 'utf-8');
    }

    public function dynamic($class)
    {
        return new Twig_Markup($this->_renderer->getHelper('dynamic')->dynamic($class), 'utf-8');
    }

    public function includeCode($position)
    {
        return new Twig_Markup($this->_renderer->getHelper('includeCode')->includeCode($position), 'utf-8');
    }

    public function image($image, $alt = '', $cssClass = null)
    {
        return new Twig_Markup($this->_renderer->getHelper('image')->image($image, $alt, $cssClass), 'utf-8');
    }

    public function multiBox($boxName)
    {
        return new Twig_Markup($this->_renderer->getHelper('multiBox')->multiBox($boxName), 'utf-8');
    }

    public function formField($vars)
    {
        $helper = new Kwf_View_Helper_FormField();
        return new Twig_Markup($helper->formField($vars), 'utf-8');
    }

    public function getComponentTemplate($componentClass)
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
                $file = $dirs[0].'/'.$componentClass;
            } else {
                foreach ($dirs as $dir) {
                    if (file_exists($dir.'/'.$componentClass)) {
                        $file = $dir.'/'.$componentClass;
                        break;
                    }
                }
            }
        }

        if ($file) {
            $ret = str_replace('_', '/', $file).'.twig';
        } else {
            $ret = null;
            foreach (explode(PATH_SEPARATOR, get_include_path()) as $ip) {
                $file = $ip.'/'.str_replace('_', '/', $componentClass).'.twig';
                if (file_exists($file)) {
                    $ret = $file;
                    break;
                }
            }
            if (!$ret) throw new Kwf_Exception("Can't find template $componentClass");
        }


        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
    }
}
