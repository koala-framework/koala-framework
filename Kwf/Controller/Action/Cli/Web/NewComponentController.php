<?php
class Kwf_Controller_Action_Cli_Web_NewComponentController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "create new component";
    }

    public function indexAction()
    {
        if (!$this->_getParam('name') || is_bool($this->_getParam('name'))) {
            throw new Kwf_Exception_Client("name has to be set!");
        }
        $type = $this->_getParam('type');
        if (!$type || is_bool($type)) {
            $type = 'Kwc_Abstract_Composite_Component';
        }
        $split = explode('/', $this->_getParam('name'));
        $name = '';
        foreach ($split as $n) {
            $name .= ucfirst($n).'/';
        }
        $name = trim($name, '/');
        if (is_dir($name)) {
            throw new Kwf_Exception_Client('Component already exists!');
        }
        $data = $this->_getPHPData($name, $type);
        mkdir('components/'.$name);
        $filename = 'components/'.$name.'/Component.php';
        file_put_contents($filename, $data);
        if ($this->_getParam('c')) {
            $filename = 'components/'.$name.'/Component.css';
            file_put_contents($filename, '');
        }
        if ($this->_getParam('j')) {
            $data = $this->_getJSData($name);
            $filename = 'components/'.$name.'/Component.js';
            file_put_contents($filename, $data);
        }
        if ($this->_getParam('t')) {
            $data = $this->_getTPLData();
            $filename = 'components/'.$name.'/Component.tpl';
            file_put_contents($filename, $data);
        }
        echo "Component created\n";
        exit;
    }
    protected function _getPHPData($name, $type)
    {
        $class = str_replace('/', '_', $name);
        $class = $class.'_Component';
        $data = "<?php\n";
        $data .= "class $class extends $type\n";
        $data .= "{\n";
        $data .= "    public static function getSettings()\n";
        $data .= "    {\n";
        $data .= "        \$ret = parent::getSettings();\n";
        $data .= "        return \$ret;\n";
        $data .= "    }\n";
        $data .= "}\n";
        return $data;
    }
    protected function _getTPLData()
    {
        $data = "<div class=\"<?=\$this->cssClass?>\">\n";
        $data .= "</div>\n";
        return $data;
    }
    protected function _getJSData($name)
    {
        $class = str_replace('/', '', $name);
        $data = "Kwf.onElementReady('.$class', function(el) {\n";
        $data .= "});";
        return $data;
    }
}
// --a : (css php und tpl)