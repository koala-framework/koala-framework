<?php
class Vps_Component_Generator_Plugin_Tags_TagsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Vps_Component_Generator_Plugin_Tags_TagsModel';
    protected $_filters = array('text'=>true);
    protected $_paging = 25;

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('text', trlVps('Tag')))
            ->setEditor(new Vps_Form_Field_TextField());
    }
}
