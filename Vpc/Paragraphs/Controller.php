<?php
class Vpc_Paragraphs_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('preview', 'Preview', 300))
            ->setRenderer('component')
            ->setData(new Vps_Auto_Data_Vpc_Frontend($this->class, $this->pageId . $this->componentKey));
        $this->_columns->add(new Vps_Auto_Grid_Column('component_class', 'Type', 200));
        $this->_columns->add(new Vps_Auto_Grid_Column('visible', 'Visible', 100))
            ->setEditor(new Vps_Auto_Field_Checkbox('visible', 'Visible'));
    }

    public function preDispatch()
    {
        $this->_components = Vpc_Admin::getInstance($this->class)->getComponents();
        parent::preDispatch();
    }
    
    public function jsonAddParagraphAction()
    {
        $class = $this->_getParam('component');
        if (array_search($class, $this->_components)) {
            $admin = Vpc_Admin::getInstance($class);
            if ($admin) { $admin->setup(); }
            $insert['page_id'] = $this->pageId;
            $insert['component_key'] = $this->componentKey;
            $insert['component_class'] = $class;
            $id = $this->_table->insert($insert);
            $where = 'page_id = ' . $this->pageId;
            $where .= ' AND component_key=\'' . $this->componentKey . '\'';
            $this->_table->numberize($id, 'pos', 0, $where);

        } else {
            $this->view->error = 'Component not found: ' . $componentClass;
        }
    }

    protected function _beforeDelete(Zend_Db_Table_Row_Abstract $row)
    {
        //$component = $this->component->getChildComponent($row->id);
        //Vpc_Admin::getInstance($component)->delete($component);
    }

/*
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
    protected $_components;


    public function jsonDataAction()
    {
        parent::jsonDataAction();
        foreach ($this->view->rows as $key => $row) {
          $src = '/admin/component/show/' . $row['component_class'] . '/' . $this->component->getId() . '-' . $row['id'];
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
*/
}