<?php
class Kwf_Component_Generator_Plugin_Tags_TagsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Kwf_Component_Generator_Plugin_Tags_TagsModel';
    protected $_filters = array('text'=>true);
    protected $_paging = 25;

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('text', trlKwf('Tag')))
            ->setEditor(new Kwf_Form_Field_TextField());
    }
}
