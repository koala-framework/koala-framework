<?php
class Vpc_NewsletterCategory_CategoriesController extends Vps_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Vpc_NewsletterCategory_CategoriesModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('category', trlVps('Category'), 200))
            ->setEditor(new Vps_Form_Field_TextField());
    }
}
