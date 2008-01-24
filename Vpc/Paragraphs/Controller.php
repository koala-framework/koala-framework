<?php
class Vpc_Paragraphs_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save' => true,
        'delete' => true,
        'reload' => true
    );
    protected $_paging = 0;
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('component_class'));
        $this->_columns->add(new Vps_Auto_Grid_Column('component_name'))
            ->setData(new Vps_Auto_Data_Vpc_ComponentName());

        $this->_columns->add(new Vps_Auto_Grid_Column('preview', 'Preview', 500))
            ->setData(new Vps_Auto_Data_Vpc_Frontend($this->class, $this->pageId . $this->componentKey))
            ->setRenderer('component');
        $this->_columns->add(new Vps_Auto_Grid_Column_Button('edit', ' ', 20))
            ->setButtonIcon('/assets/silkicons/table_edit.png')
            ->setToolTip('Edit Paragraph');
        $this->_columns->add(new Vps_Auto_Grid_Column('visible', '', 20))
            ->setEditor(new Vps_Auto_Field_Checkbox('visible', 'Visible'));
    }

    public function preDispatch()
    {
        $this->_components = array();
        foreach (Vpc_Abstract::getSetting($this->class, 'childComponentClasses') as $c) {
            $this->_components[Vpc_Abstract::getSetting($c, 'componentName')] = $c;
        }
        parent::preDispatch();
    }

    public function jsonAddParagraphAction()
    {
        $class = $this->_getParam('component');
        if (array_search($class, $this->_components)) {
            $admin = Vpc_Admin::getInstance($class);
            if ($admin) $admin->setup();
            $insert['page_id'] = $this->pageId;
            $insert['component_key'] = $this->componentKey;
            $insert['component_class'] = $class;
            $id = $this->_table->insert($insert);
            $where['page_id = ?'] = $this->pageId;
            $where['component_key = ?'] = $this->componentKey;
            $this->_table->numberize($id, 'pos', 0, $where);
        } else {
            throw new Vps_Exception("Component '$class' not found");
        }
    }
}
