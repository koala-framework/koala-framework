<?php
class Kwf_Controller_Action_Cli_Web_ScaffoldController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "create new component, model or controller";
    }

    public function indexAction()
    {
        echo "What do you want to create? [COMPONENT/model/controller]: ";
        $stdin = fopen('php://stdin', 'r');
        $type = trim(strtolower(fgets($stdin)));
        if (!$type) {
            $type = 'component';
        }
        if ($type == 'component') {
            $this->_createComponent();
        } else if ($type == 'model') {
            $this->_createModel();
        } else if ($type == 'controller') {
            $this->_createController();
        }
        echo $type." created\n";
        exit;
    }

    protected function _createComponent()
    {
        $create = array(
            'css' => false,
            'js' => array(
                'general' => false,
                'config' => false,
            ),
            'tpl' => false,
        );
        echo "enter component path: ";
        $stdin = fopen('php://stdin', 'r');
        $input = trim(strtolower(fgets($stdin)));
        if (!$input) {
            throw new Kwf_Exception_Client('component must have a name');
        }
        $split = explode('/', $input);
        $path = 'components/';
        foreach ($split as $n) {
            $path .= ucfirst($n).'/';
        }
        $path = trim($path, '/');
        if (is_dir($path)) {
//             throw new Kwf_Exception_Client('Component already exists!');
        }
        mkdir($path);
        echo "enter class to extend from [no entry for default]: ";
        $stdin = fopen('php://stdin', 'r');
        $extends = '';
        $input = trim(strtolower(fgets($stdin)));
        if ($input) {
            $split = explode('_', $input);
            foreach ($split as $s) {
                $extends .= ucfirst($s).'_';
            }
            $extends = trim($extends,'_');
        } else {
            $extends = 'Kwc_Abstract';
        }
        echo "create css file? [y/N]: ";
        $stdin = fopen('php://stdin', 'r');
        $input = trim(strtolower(fgets($stdin)));
        if (!($input == '' || $input == 'n')) {
            $create['css'] = true;
        }
        echo "create js file? [y/N]: ";
        $stdin = fopen('php://stdin', 'r');
        $input = trim(strtolower(fgets($stdin)));
        if (!($input == '' || $input == 'n')) {
            $create['js']['general'] = true;

            echo "assume a config array from php? [y/N]: ";
            $stdin = fopen('php://stdin', 'r');
            $input = trim(strtolower(fgets($stdin)));
            if (!($input == '' || $input == 'n')) {
                $create['js']['config'] = true;
            }
        }
        echo "create tpl file? [y/N]: ";
        $stdin = fopen('php://stdin', 'r');
        $input = trim(strtolower(fgets($stdin)));
        if (!($input == '' || $input == 'n')) {
            $create['tpl'] = true;
        }
        $name = str_replace('components/', '', $path);
        $this->_writeComponentPHPFile($name, $create, $extends);
        if ($create['css']) {
            $this->_writeComponentCSSFile($name, $create);
        }
        if ($create['tpl']) {
            $this->_writeComponentTPLFile($name, $create);
        }
        if ($create['js']['general']) {
            $this->_writeComponentJSFile($name, $create);
        }

    }
    protected function _writeComponentPHPFile($name, $create, $extends)
    {
        $settings = '';
        if ($create['js']['general']) {
            $settings .= "        \$ret['assets']['files'][] = 'web/components/$name/Component.js';";
        }
        $templateVars = '';
        if ($create['js']['config']) {
            $templateVars .= "    public function getTemplateVars()\n";
            $templateVars .= "    {\n";
            $templateVars .= "        \$ret = parent::getTemplateVars();\n";
            $templateVars .= "        \$ret['config'] = array();\n";
            $templateVars .= "        return \$ret;\n";
            $templateVars .= "    }\n";
        }
        $class = str_replace('/', '_', $name);
        $class = $class.'_Component';
        $data = "<?php\n";
        $data .= "class $class extends $extends\n";
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
        $filename = 'components/'.$name.'/Component.php';
        file_put_contents($filename, $data);
    }
    protected function _writeComponentCSSFile($name, $create)
    {
        $filename = 'components/'.$name.'/Component.css';
        file_put_contents($filename, "\n");
    }
    protected function _writeComponentJSFile($name, $create)
    {
        $config = '';
        if ($create['js']['config']) {
            $config .= ', config';
        }
        $class = str_replace('/', '', $name);
        $data = "Kwf.onElementReady('.$class', function(el$config) {\n";
        $data .= "});";
        $filename = 'components/'.$name.'/Component.js';
        file_put_contents($filename, $data);
    }
    protected function _writeComponentTPLFile($name, $create)
    {
        $content = '';
        if ($create['js']['config']) {
            $content .= "    <input type=\"hidden\" value=\"<?=htmlspecialchars(json_encode(\$this->config))?>\" />\n";
        }
        $data = "<div class=\"<?=\$this->cssClass?>\">\n";
        $data .= $content;
        $data .= "</div>\n";
        $filename = 'components/'.$name.'/Component.tpl';
        file_put_contents($filename, $data);
    }

    protected function _createModel()
    {
        echo "coming soon\n";
    }
    protected function _createController()
    {
        echo "coming soon\n";
    }
}
