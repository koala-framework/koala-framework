<?php
class Vpc_Paragraphs_IndexController extends Vps_Controller_Action_Auto_Grid
{
    protected $_columns = array(
            array('dataIndex' => 'page_id',
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
                  'header'    => 'Sichtbar',
                  'editor'    => 'Checkbox')
            );
    protected $_buttons = array(
        'save' => true,
        'delete' => true
    );
    protected $_paging = 0;
    protected $_defaultOrder = 'pos';
    protected $_tableName = 'Vpc_Paragraphs_IndexModel';
    protected $_jsClass = 'Vpc.Paragraphs.Index';
    protected $_components;

    public function init()
    {
        parent::init();
        $this->_components = Vpc_Setup_Abstract::getAvailableComponents('Vpc/');
    }
    
    public function indexAction()
    {
        $componentList = array();
        foreach ($this->_components as $name => $component) {
            $str = '$componentList["' . str_replace('.', '"]["', $name) . '"] = "' . $component . '";';
            eval($str);
        }
        $config = array('components' => $componentList);
        $this->view->ext($this->_jsClass, $config);
    }

    public function jsonIndexAction()
    {
        $this->indexAction();
    }

    public function jsonDataAction()
    {
        parent::jsonDataAction();
        foreach ($this->component->getChildComponents() as $key => $c) {
            $src = '/component/show/' . get_class($c) . '/' . $c->getId() . '/';
            if (isset($this->view->rows[$key]['page_id'])) {
                $this->view->rows[$key]['page_id'] = $src;
            }
        }
        $components = $this->_components;
        foreach ($this->view->rows as $key => $val) {
            $componentClass = array_search($val['component_class'], $components);
            $componentClass = str_replace('.', ' -> ', $componentClass);
            if ($componentClass == '') {
                $componentClass = $val;
            }
            if (isset($this->view->rows[$key]['component_class'])) {
                $this->view->rows[$key]['component_class'] = $componentClass;
            }
        }
    }

    public function jsonAddParagraphAction()
    {
        $componentName = $this->_getParam('component');
        if (array_search($componentName, $this->_components) && is_subclass_of($componentName, 'Vpc_Abstract')) {
            $class = $componentName;
            while ($class != 'Vpc_Abstract') {
                $len = strlen(strrchr($class, '_'));
                $setupClass = substr($class, 0, -$len) . '_Setup';
                try {
                    if (class_exists($setupClass)) {
                        $setup = new $setupClass($this->_table->getAdapter());
                        $setup->setup();
                    }
                } catch (Zend_Exception $e) {
                }
                $class = get_parent_class($class);
            }

            $insert['page_id'] = $this->component->getDbId();
            $insert['component_key'] = $this->component->getComponentKey();
            $insert['component_class'] = $componentName;
            $id = $this->_table->insert($insert);
            $where = 'page_id = ' . $this->component->getDbId();
            $where .= ' AND component_key=\'' . $this->component->getComponentKey() . '\'';
            $this->_table->numberize($id, 'pos', 0, $where);
            
        } else {
            $this->view->error = 'Component not found: ' . $componentName;
        }
    }

    protected function _beforeSave($row)
    {
        $row->page_id = $this->component->getDbId();
        $row->component_key = $this->component->getComponentKey();
        $row->save();
    }

    protected function _getWhere()
    {
        $where = parent::_getWhere();
        $where['page_id = ?'] = $this->component->getDbId();
        $where['component_key = ?'] = $this->component->getComponentKey();
        return $where;
    }

    private function _getPosition()
    {
        $where = array();
        $where['page_id = ?']  = $this->component->getDbId();
        $where['component_key = ?']  = $this->component->getComponentKey();
        $rows = $this->_table->fetchAll($where);
        
        $ids = array();
        foreach ($rows as $rowKey => $rowData){
            $id =$rowData->pos;
            $ids[] = $id;
        }
        rsort($ids);
        if ($ids == array()) $id = 1;
        else $id = $ids[0] + 1;
        return $id;
    }

}
