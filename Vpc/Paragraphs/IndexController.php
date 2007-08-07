<?php
class Vpc_Paragraphs_IndexController extends Vps_Controller_Action_Auto_Grid
{
    protected $_columns = array(
            array('dataIndex' => 'id',
                  'header'    => 'Vorschau',
                  'type'      => 'string',
                  'width'     => 410,
                  'renderer'  => 'Component'),
            array('dataIndex' => 'component_class',
                  'header'    => 'Komponente',
                  'width'     => 200),
            array('dataIndex' => 'pos',
                  'header'    => 'Position',
                  'width'     => 50),
            array('dataIndex' => 'visible',
                  'header'    => 'Sichtbar')
            );
    protected $_buttons = array('add' => true,
                                'delete' => true);
    protected $_paging = 0;
    protected $_defaultOrder = 'pos';
    protected $_tableName = 'Vpc_Paragraphs_IndexModel';
    private $_ini;

    public function init()
    {
        parent::init();
        $this->_ini = new Vps_Config_Ini('application/components.ini');
    }
    public function indexAction()
    {
        $components = array();
        foreach ($this->_ini->toArray() as $component => $data) {
            $str = '$components["' . str_replace('.', '"]["', $component) . '"] = "' . $component . '";';
            eval($str);
        }
        
        $config = array('components' => $components);
        $this->view->ext('Vpc.Paragraphs.Index', $config);
    }

    public function jsonIndexAction()
    {
        $this->indexAction();
    }

    public function jsonDataAction()
    {
        parent::jsonDataAction();
        foreach ($this->component->getChildComponents() as $key => $c) {
            $src = '/component/show/' . $c->getId() . '/';
            $this->view->rows[$key]['id'] = $src;
        }
    }

    protected function _getWhere()
    {
        $where = array();
        $where[] = "page_id='" . $this->component->getDbId() . "'";
        $where[] = "component_key='" . $this->component->getComponentKey() . "'";
        return $where;
    }
    
    public function jsonAddParagraphAction()
    {
        $componentName = $this->_getParam('component');
        $ini = $this->_ini->$componentName;
        if ($ini) {
            $class = $ini->class;
            try {
                $setupClass = str_replace('_Index', '_Setup', $class);
                if (class_exists($setupClass)) {
                    $setup = new $setupClass($this->getAdapter());
                    $setup->setup();
                }
            } catch (Zend_Exception $e) {
            }
    
            $config = call_user_func(array($class, 'getStaticSettings')); 
            foreach ($config as $element => $value){
                if (!$this->_ini->checkKeyExists($class, $element)) {
                    $this->_ini->setValue($class, $element, (string)$value);       
                }       
            }     
            $this->_ini->write();
    
            $insert['page_id'] = $this->component->getDbId();
            $insert['component_key'] = $this->component->getComponentKey();
            $insert['component'] = $componentName;
            
        } else {
            $this->view->error = 'Component not found: ' . $componentName;
        }
    }

}
