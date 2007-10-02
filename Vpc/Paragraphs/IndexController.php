<?php
class Vpc_Paragraphs_IndexController extends Vps_Controller_Action_Auto_Grid
{
    protected $_columns = array(
            array('dataIndex' => 'page_id',
                  'header'    => 'Vorschau',
                  'type'      => 'string',
                  'width'     => 410,
                  'renderer'  => 'component'),
            array('dataIndex' => 'component_class',
                  'header'    => 'Komponente',
                  'width'     => 200),
            array('dataIndex' => 'visible',
                  'header'    => 'Sichtbar',
                  'editor'    => 'Checkbox',
                  'width'     => 30)
            );
    protected $_buttons = array(
        'save' => true,
        'delete' => true,
        'reload' => true
    );
    protected $_paging = 0;
    protected $_position = 'pos';
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
    
    protected function _beforeDelete(Zend_Db_Table_Row_Abstract $row)
    {
        Vpc_Setup_Abstract::staticDelete($row->component_class, $row->page_id, $row->component_key . '-' . $row->id);
    }

    public function jsonDataAction()
    {
        parent::jsonDataAction();
        foreach ($this->view->rows as $key => $row) {
          $src = '/component/show/' . $row['component_class'] . '/' . $this->component->getId() . '-' . $row['id'] . '/';
            $this->view->rows[$key]['page_id'] = $src;

            $componentClass = array_search($row['component_class'], $this->_components);
            $componentClass = str_replace('.', ' -> ', $componentClass);
            if ($componentClass == '') {
                $componentClass = $row;
            }
            if (isset($this->view->rows[$key]['component_class'])) {
                $this->view->rows[$key]['component_class'] = $componentClass;
            }
        }
    }

    public function jsonAddParagraphAction()
    {
        $componentClass = $this->_getParam('component');
        if (array_search($componentClass, $this->_components)) {
            Vpc_Setup_Abstract::staticSetup($componentClass);
            $insert['page_id'] = $this->component->getDbId();
            $insert['component_key'] = $this->component->getComponentKey();
            $insert['component_class'] = $componentClass;
            $id = $this->_table->insert($insert);
            $where = 'page_id = ' . $this->component->getDbId();
            $where .= ' AND component_key=\'' . $this->component->getComponentKey() . '\'';
            $this->_table->numberize($id, 'pos', 0, $where);

        } else {
            $this->view->error = 'Component not found: ' . $componentClass;
        }
    }

    protected function _beforeSave($row)
    {
        $row->page_id = $this->component->getDbId();
        $row->component_key = $this->component->getComponentKey();
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
