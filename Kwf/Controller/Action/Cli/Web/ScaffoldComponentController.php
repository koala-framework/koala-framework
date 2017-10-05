<?php
class Kwf_Controller_Action_Cli_Web_ScaffoldComponentController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "create new component";
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'class',
                'value'=> 'classname',
                'valueOptional' => false,
            ),
            array(
                'param'=> 'parent',
                'value'=> 'name from extended class',
                'allowBlank' => true,
            ),
            array(
                'param'=> 't',
                'value'=> 'create template file',
                'allowBlank' => true,
            ),
            array(
                'param'=> 'c',
                'value'=> 'create css file',
                'allowBlank' => true,
            ),
            array(
                'param'=> 'j',
                'value'=> 'create js file',
                'allowBlank' => true,
            ),
        );
    }

    public function indexAction()
    {
        if (!$this->_getParam('class') || is_bool($this->_getParam('class'))) {
            throw new Kwf_Exception_Client("class has to be set!");
        }
        $js = false;
        if ($this->_getParam('j')) {
            $js = true;
        }
        $type = $this->_getParam('parent');
        if (!$type || is_bool($type)) {
            $type = 'Kwc_Abstract_Composite_Component';
        }
        $split = explode('/', $this->_getParam('class'));
        $name = '';
        foreach ($split as $n) {
            $name .= ucfirst($n).'/';
        }
        $name = trim($name, '/');
        if (is_dir($name)) {
            throw new Kwf_Exception_Client('Component already exists!');
        }
        $data = $this->_getPHPData($name, $type, $js);
        mkdir('components/'.$name);
        $filename = 'components/'.$name.'/Component.php';
        file_put_contents($filename, $data);
        if ($this->_getParam('c')) {
            $filename = 'components/'.$name.'/Component.css';
            file_put_contents($filename, '');
        }
        if ($js) {
            $data = $this->_getJSData($name);
            $filename = 'components/'.$name.'/Component.js';
            file_put_contents($filename, $data);
        }
        if ($this->_getParam('t')) {
            $data = $this->_getTPLData($js);
            $filename = 'components/'.$name.'/Component.tpl';
            file_put_contents($filename, $data);
        }
        echo "Component created\n";
        exit;
    }
    protected function _getPHPData($name, $type, $js)
    {
        $settings = '';
        if ($js) {
        }
        $templateVars = '';
        $class = str_replace('/', '_', $name);
        $class = $class.'_Component';
        $data = "<?php\n";
        $data .= "class $class extends $type\n";
        $data .= "{\n";
        $data .= "    public static function getSettings()\n";
        $data .= "    {\n";
        $data .= "        \$ret = parent::getSettings();\n";
        if ($settings) {
            $data .= "$settings\n";
        }
        $data .= "        return \$ret;\n";
        $data .= "    }\n";
        $data .= $templateVars;
        $data .= "}\n";
        return $data;
    }
    protected function _getTPLData($js)
    {
        $content = '';
        if ($js) {
            $content .= "    <input type=\"hidden\" value=\"<?=Kwf_Util_HtmlSpecialChars::filter(json_encode(\$this->config))?>\" />\n";
        }
        $data = "<div class=\"<?=\$this->cssClass?>\">\n";
        $data .= $content;
        $data .= "</div>\n";
        return $data;
    }
    protected function _getJSData($name)
    {
        $class = str_replace('/', '', $name);
        $data = "Kwf.onElementReady('.$class', function(el, config) {\n";
        $data .= "});";
        return $data;
    }
}
