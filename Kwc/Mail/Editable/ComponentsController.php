<?php
class Kwc_Mail_Editable_ComponentsController_EditComponentsData extends Kwf_Data_Abstract
{
    public function load($row, array $info = array())
    {
        $admin = Kwc_Admin::getInstance($row->content_component_class);
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

class Kwc_Mail_Editable_ComponentsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_modelName = 'Kwc_Mail_Editable_ComponentsModel';

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Name'), 290));
        $this->_columns->add(new Kwf_Grid_Column('settings_controller_url'));
        $this->_columns->add(new Kwf_Grid_Column('preview_controller_url'));
        $this->_columns->add(new Kwf_Grid_Column('edit_components'))
            ->setData(new Kwc_Mail_Editable_ComponentsController_EditComponentsData());
    }
}
