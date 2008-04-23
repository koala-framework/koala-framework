<?php
class Vpc_Paragraphs_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save',
        'delete',
        'reload',
        'addparagraph'
        );
    protected $_paging = 0;
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Auto_Grid_Column('component_class'));
        $this->_columns->add(new Vps_Auto_Grid_Column('component_name'))
            ->setData(new Vps_Auto_Data_Vpc_ComponentName());

        $this->_columns->add(new Vps_Auto_Grid_Column('preview', trlVps('Preview'), 500))
            ->setData(new Vps_Auto_Data_Vpc_Frontend($this->class, $this->componentId))
            ->setRenderer('component');
        $this->_columns->add(new Vps_Auto_Grid_Column_Visible());
        $this->_columns->add(new Vps_Auto_Grid_Column_Button())
            ->setButtonIcon(new Vps_Asset('paragraph_edit'))
            ->setTooltip(trlVps('Edit Paragraph'));
    }

    public function preDispatch()
    {
        $this->_components = array();
        foreach (Vpc_Abstract::getSetting($this->class, 'childComponentClasses') as $c) {
            $name = Vpc_Abstract::getSetting($c, 'componentName');
            if (!$name) $name = Vpc_Abstract::getSetting($c, 'name');
            if ($name) $this->_components[$name] = $c;
        }
        parent::preDispatch();
    }

    public function jsonAddParagraphAction()
    {
        $class = $this->_getParam('component');
        if (array_search($class, $this->_components)) {
            $admin = Vpc_Admin::getInstance($class);
            if ($admin) $admin->setup();
            $row = $this->_model->createRow();
            $row->component_id = $this->componentId;
            $row->component_class = $class;
            $row->pos = 1000; //TODO: bessere L�sung mit Vps_Filter_Row_Numberize
            $row->visible = 0;
            $row->save();
            $id = $row->id;
            $where['component_id = ?'] = $this->componentId;
            $this->_model->getTable()->numberize($id, 'pos', null, $where);

            // Hack für weiterleiten auf Edit-Seite
            $name = Vpc_Abstract::getSetting($this->_model->getTable()->getComponentClass(), 'componentName');
            $name = str_replace('.', ' -> ', $name);
            $data = $this->_model->find($id)->current()->getRow()->toArray();
            $this->view->data = $data;
            $this->view->data['component_name'] = $name;

            $this->view->hasController = !is_null(
                Vpc_Admin::getComponentFile($data['component_class'], 'Controller')
            );
        } else {
            throw new Vps_Exception("Component $class not found");
        }
    }
}
