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
            ->setData(new Vps_Auto_Data_Vpc_Frontend($this->class, $this->componentId))
            ->setRenderer('component');
        $this->_columns->add(new Vps_Auto_Grid_Column_Button('edit', ' ', 20))
            ->setButtonIcon('/assets/silkicons/table_edit.png')
            ->setToolTip('Edit Paragraph');
        $this->_columns->add(new Vps_Auto_Grid_Column_Visible());
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
            $insert['component_id'] = $this->componentId;
            $insert['component_class'] = $class;
            $id = $this->_table->insert($insert);
            $where['component_id = ?'] = $this->componentId;
            $this->_table->numberize($id, 'pos', null, $where);

            // Hack für weiterleiten auf Edit-Seite
            $name = Vpc_Abstract::getSetting($this->_table->getComponentClass(), 'componentName');
            $name = str_replace('.', ' -> ', $name);
            $this->view->data = $this->_table->find($id)->current()->toArray();
            $this->view->data['component_name'] = $name;
        } else {
            throw new Vps_Exception("Component '$class' not found");
        }
    }
}
