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
    private $_components;

    public function init()
    {
        parent::init();
        $this->_components = Vpc_Setup_Abstract::getAvailableComponents('Vpc/');
    }
    public function indexAction()
    {
        $componentList = array();
        foreach ($this->_components as $component) {
            $name = constant("$component::NAME");
            $str = '$componentList["' . str_replace('.', '"]["', $name) . '"] = "' . $component . '";';
            eval($str);
        }

        $config = array('components' => $componentList);
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
        if (array_search($componentName, $this->_components)) {
            try {
                $setupClass = str_replace('_Index', '_Setup', get_class($this->component));
                if (class_exists($setupClass)) {
                    $setup = new $setupClass($this->getAdapter());
                    $setup->setup();
                }
            } catch (Zend_Exception $e) {
            }
    
            $insert['page_id'] = $this->component->getDbId();
            $insert['component_key'] = $this->component->getComponentKey();
            $insert['component_class'] = $componentName;
            $this->_table->insert($insert);
            
        } else {
            $this->view->error = 'Component not found: ' . $componentName;
        }
    }
    
    

}
