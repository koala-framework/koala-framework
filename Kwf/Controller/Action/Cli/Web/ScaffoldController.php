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
        } else {
            throw new Kwf_Exception_Client('wrong type');
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
            throw new Kwf_Exception_Client('Component already exists!');
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
        }
        $templateVars = '';
        if ($create['js']['config']) {
            $templateVars .= "    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)\n";
            $templateVars .= "    {\n";
            $templateVars .= "        \$ret = parent::getTemplateVars($renderer);\n";
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
        $class = strtolower(str_replace('/', '', $name));
        $data = "Kwf.onElementReady('.$class', function(el$config) {\n";
        $data .= "});";
        $filename = 'components/'.$name.'/Component.js';
        file_put_contents($filename, $data);
    }
    protected function _writeComponentTPLFile($name, $create)
    {
        $content = '';
        if ($create['js']['config']) {
            $content .= "    <input type=\"hidden\" value=\"<?=Kwf_Util_HtmlSpecialChars::filter(json_encode(\$this->config))?>\" />\n";
        }
        $data = "<div class=\"<?=\$this->rootElementClass?>\">\n";
        $data .= $content;
        $data .= "</div>\n";
        $filename = 'components/'.$name.'/Component.tpl';
        file_put_contents($filename, $data);
    }

    protected function _createModel()
    {
        echo "enter model name: ";
        $stdin = fopen('php://stdin', 'r');
        $name = ucfirst(trim(strtolower(fgets($stdin))));

        if (!$name) {
            throw new Kwf_Exception_Client('model must have a name');
        }
        echo "place the model into a component directory? [y/N]: ";
        $stdin = fopen('php://stdin', 'r');
        $input = trim(strtolower(fgets($stdin)));
        if (!($input == '' || $input == 'n')) {
                echo "enter path of the component: ";
            $stdin = fopen('php://stdin', 'r');
            $path = trim(strtolower(fgets($stdin)));
            $split = explode('/', $path);
            $path = '';
            foreach ($split as $s) {
                $path .= ucfirst($s).'/';
            }
            $filename = 'components/'.$path.$name.'.php';
            $className = str_replace('/', '_', $path).$name;
        } else {
            $filename = 'models/'.$name.'.php';
            $classname = $name;
        }
        $childRows = array();
        $parentRows = array();
        do {
            echo "enter a label for a dependent model: ";
            $stdin = fopen('php://stdin', 'r');
            $input = trim(strtolower(fgets($stdin)));
            if ($input) {
                echo "enter the name of the dependent model: ";
                $stdin = fopen('php://stdin', 'r');
                $model = trim(strtolower(fgets($stdin)));
            }
            if ($input && $model) $childRows[ucfirst($input)] = ucfirst($model);
        } while ($input && $model);
        do {
            echo "enter a label for a referenced model: ";
            $stdin = fopen('php://stdin', 'r');
            $label = trim(strtolower(fgets($stdin)));
            if ($label) {
                echo "enter the model class for the referenced model: ";
                $stdin = fopen('php://stdin', 'r');
                $parent = trim(strtolower(fgets($stdin)));
            }
            if ($label && $parent) {
                echo "enter the column for the referenced model: ";
                $stdin = fopen('php://stdin', 'r');
                $column = trim(strtolower(fgets($stdin)));
            }
            if ($label && $parent && $column) $parentRows[$label] = array(
                'modelClass' => ucfirst($parent),
                'column' => $column
                );
        } while ($label && $parent && $column);
        $tableName = strtolower($name);
        $table = "    protected \$_table = '$tableName';\n";
        $referenceMap = '';
        $dependentModels = '';
        if ($childRows) {
            $dependentModels = "    protected \$_dependentModels = array(\n";
            foreach ($childRows as $k => $cR) {
                $dependentModels .= "        '$k' => '$cR',\n";
            }
            $dependentModels .= "    );\n";
        }
        if ($parentRows) {
            $referenceMap = "    protected \$_referenceMap = array(\n";
            foreach ($parentRows as $k => $pR) {
                $modelClass = $pR['modelClass'];
                $column = $pR['column'];
                $referenceMap .= "        '$k' => array(\n";
                $referenceMap .= "            'refModelClass' => '$modelClass',\n";
                $referenceMap .= "            'column' => '$column',\n";
                $referenceMap .= "        ),\n";
            }
            $referenceMap .= "    );\n";

        }
        $data = "<?php\n";
        $data .= "class $className extends Kwf_Model_Db\n";
        $data .= "{\n";
        $data .= $table;
        $data .= $referenceMap;
        $data .= $dependentModels;
        $data .= "}\n";
        file_put_contents($filename, $data);
    }
    protected function _createController()
    {
        echo "enter controller name: ";
        $stdin = fopen('php://stdin', 'r');
        $name = ucfirst(trim(strtolower(fgets($stdin))));
        if (!$name) {
            throw new Kwf_Exception_Client('controller must have a name');
        }
        $allowedComponent = '';
        echo "place the controller into a component directory? [y/N]: ";
        $stdin = fopen('php://stdin', 'r');
        $input = trim(strtolower(fgets($stdin)));
        if (!($input == '' || $input == 'n')) {
            echo "enter path of the component: ";
            $stdin = fopen('php://stdin', 'r');
            $path = trim(strtolower(fgets($stdin)));
            $split = explode('/', $path);
            $path = '';
            foreach ($split as $s) {
                $path .= ucfirst($s).'/';
            }
            $filename = 'components/'.$path.$name.'Controller.php';
            $className = str_replace('/', '_', $path).$name.'Controller';
            $allowedComponent = "    protected function _isAllowedComponent()\n";
            $allowedComponent .= "    {\n";
            $allowedComponent .= "        return true;\n";
            $allowedComponent .= "    }\n";
        } else {
            echo "enter path of the controller: ";
            $stdin = fopen('php://stdin', 'r');
            $controllerDirectory = trim(strtolower(fgets($stdin)));
            $dirName = ucfirst($controllerDirectory);
            if (!is_dir('controllers')) mkdir('controllers');
            if (!is_dir('controllers/'.$dirName)) mkdir('controllers/'.$dirName);
            $filename = 'controllers/'.ucfirst($controllerDirectory).'/'.$name.'Controller.php';
            $className = ucfirst($controllerDirectory).'_'.$name.'Controller';
            $this->_checkControllerDirectory($controllerDirectory);
        }
        echo "enter type of the controller [form/grid]: ";
        $stdin = fopen('php://stdin', 'r');
        $input = trim(strtolower(fgets($stdin)));
        if ($input == 'form') {
            $form = true;
            $extends = 'Kwf_Controller_Action_Auto_Form';
            $initFunction = "    protected function _initFields()\n";
            $initFunction .= "    {\n";
            $initFunction .= "    }\n";
        } else if ($input == 'grid') {
            $form = false;
            $extends = 'Kwf_Controller_Action_Auto_Grid';
            $initFunction = "    protected function _initColumns()\n";
            $initFunction .= "    {\n";
            $initFunction .= "    }\n";
        } else {
            $extends = 'Kwf_Controller_Action';
            $initFunction = '';
        }
        echo "enter model name used from the controller: ";
        $stdin = fopen('php://stdin', 'r');
        $modelName = trim(strtolower(fgets($stdin)));
        $modelName = ucfirst($modelName);
        $model = '';
        if ($modelName) {
            if ($form) {
                $model = "    protected \$_modelName = '$modelName';\n";
            } else {
                $model = "    protected \$_model = '$modelName';\n";
            }
        }
        $data = "<?php\n";
        $data .= "class $className extends $extends\n";
        $data .= "{\n";
        $data .= $model;
        $data .= $initFunction;
        $data .= $allowedComponent;
        $data .= "}\n";
        file_put_contents($filename, $data);
    }
    protected function _checkControllerDirectory($directory)
    {
        $dir = ucfirst($directory);
        $bootstrap = file_get_contents('bootstrap.php');
        $pattern = 'controllers/'.$directory;
        if (!preg_match("#addcontrollerdirectory\('$pattern#", strtolower($bootstrap))) {
//             !preg_match('#addControllerDirectory("controllers/'.$directory.'#', $bootstrap)) {
            echo "didn't found module $directory in bootstrap. Do you want to create it? [Y/n]: ";
            $stdin = fopen('php://stdin', 'r');
            $input = trim(strtolower(fgets($stdin)));
            if ($input == '' || $input == 'y' || $input == 'j') {
                do {
                    echo "enter a module name for $directory: ";
                    $stdin = fopen('php://stdin', 'r');
                    $moduleName = trim(strtolower(fgets($stdin)));
                } while (!$moduleName);
                //register the controllerDirectory in bootstrap
                $addDirectory = "\$front->addControllerDirectory('controllers/$dir', '$moduleName');";
                $newBootstrap = preg_replace('#Controller_Front_Component::getInstance\(\);#',"Controller_Front_Component::getInstance();\n$addDirectory\n", $bootstrap);
                file_put_contents('bootstrap.php', $newBootstrap);
            }
        }
    }
}
