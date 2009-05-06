<?php
class Vps_Controller_Action_Component_OverviewController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('addComponent', 'createTpl', 'createCss', 'reload');

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('class', 'Class', 100));
        $this->_columns->add(new Vps_Grid_Column('name', 'Name', 100));
        $this->_columns->add(new Vps_Grid_Column('parentClass', 'Parent Class', 100));
        $this->_columns->add(new Vps_Grid_Column('hasTemplate', 'web tpl', 50))
                                ->setRenderer('boolean');
        $this->_columns->add(new Vps_Grid_Column('hasCss', 'web css', 50))
                                ->setRenderer('boolean');
    }

    public function indexAction()
    {
        $this->view->ext('Vps.Component.Overview', array('controllerUrl'=>'/admin/component/overview'));
    }
    protected function _appendMetaData()
    {
        parent::_appendMetaData();
        $c = Vpc_Admin::getAvailableComponents();
        sort($c);
        $this->view->metaData['components'] = $c;
    }

    protected function _fetchData($order, $limit, $start)
    {
        $usedComponents = Vpc_Admin::getAvailableComponents('./Vpc');
        $ret = array();
        foreach ($usedComponents as $c) {
            $name = Vpc_Abstract::getSetting($c, 'componentName');
            $path = str_replace('_', '/', $c);
            $ret[] = array('class'=>$c,
                           'name'=>$name,
                           'parentClass'=>get_parent_class($c),
                           'hasTemplate' => file_exists($path.'.tpl'),
                           'hasCss' => file_exists($path.'.css'),
                           );
        }
        return $ret;
    }

    public function jsonCreateAction()
    {
        $class = $this->_getParam('class');
        $type = $this->_getParam('type');
        if (!in_array($type, array('tpl', 'css'))) {
            throw new Vps_Exception("Invalid type");
        }
        $path = str_replace('_', '/', $class);
        $path = str_replace('.', '', $path); //security
        if (file_exists($path.'.'.$type)) {
            throw new Vps_ClientException("File does already exist.");
        }
        $srcFile = Vpc_Admin::getComponentFile($class, $type);
        if (!$srcFile) {
            throw new Vps_ClientException("No file exists that could be copied.");
        }
        if (!copy($srcFile, $path.'.'.$type)) {
            throw new Vps_Exception("Can't copy '$srcFile' to '$path.$type'");
        }
        $this->view->path = $path.'.'.$type;
    }

    public function jsonAddComponentAction()
    {
        $class = $this->_getParam('class');
        $name = $this->_getParam('name');
        $content  = "<?php\n";
        $content .= "class $name extends $class\n";
        $content .= "{\n";
        $content .= "}\n";
        $path = str_replace('_', '/', $name);
        $path = str_replace('.', '', $path); //security
        if (file_exists($path.'.php')) {
            throw new Vps_ClientException("File does allready exist.");
        }
        mkdir(substr($path, 0, strrpos($path, '/')), 0777, true);
        if (!file_put_contents($path.'.php', $content)) {
            throw new Vps_Exception("Can't create '$path.php'");
        }
        $this->view->path = $path.'.php';

        Vpc_Admin::getInstance($name)->setup();
    }
}
