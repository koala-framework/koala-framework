<?php
class Vpc_Mail_Editable_ComponentsController_EditComponentsData extends Vps_Data_Abstract
{
    public function load($row)
    {
        $admin = Vpc_Admin::getInstance($row->content_component_class);
        $ret = array();
        foreach ($admin->getExtConfig() as $k=>$cfg) {
            $ret[] = array(
                'componentClass' => $row->content_component_class,
                'type' => $k
            );
        }
        return $ret;
    }
}

class Vpc_Mail_Editable_ComponentsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_modelName = 'Vpc_Mail_Editable_ComponentsModel';

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('name', trlVps('Name'), 150));
        $this->_columns->add(new Vps_Grid_Column('settings_controller_url'));
        $this->_columns->add(new Vps_Grid_Column('edit_components'))
            ->setData(new Vpc_Mail_Editable_ComponentsController_EditComponentsData());
    }
}
