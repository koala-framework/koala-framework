<?php
class Vpc_Newsletter_Controller extends Vpc_Directories_Item_Directory_Controller
{
    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('from_email', 'from', 80))
            ->setEditor(new Vps_Form_Field_TextField());
        $this->_columns->add(new Vps_Grid_Column('subject', trlVps('Subject'), 180))
            ->setData(new Vpc_Newsletter_Controller_Data())
            ->setEditor(new Vps_Form_Field_TextField());
        $this->_columns->add(new Vps_Grid_Column_Button('properties', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper.png')
            ->setTooltip(trlVps('Properties'));
        $this->_columns->add(new Vps_Grid_Column_Button('edit', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper_go.png')
            ->setTooltip(trlVps('Edit News'));
        parent::_initColumns();
    }

    public function indexAction()
    {
        parent::indexAction();
        $this->view->componentConfigs['Vpc_Newsletter_Component-items']['idTemplate'] = $this->_getParam('componentId') . '_{0}';
    }
}

class Vpc_Newsletter_Controller_Data extends Vps_Data_Table
{
    public function load($row)
    {
        return 'foo';
    }
}
