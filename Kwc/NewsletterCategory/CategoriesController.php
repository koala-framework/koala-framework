<?php
class Kwc_NewsletterCategory_CategoriesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Kwc_NewsletterCategory_CategoriesModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('category', trlKwf('Category'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
    }
}
