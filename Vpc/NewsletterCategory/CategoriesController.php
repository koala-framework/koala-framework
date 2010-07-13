<?php
class Vpc_NewsletterCategory_CategoriesController extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_modelName = 'Vpc_NewsletterCategory_CategoriesModel';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $select = new Vps_Form_Field_PoolSelect();
        $select->setPool('Newsletterkategorien');
        $this->_columns->add(new Vps_Grid_Column('category', trlVps('Category'), 200))
            ->setEditor(new Vps_Form_Field_TextField());
        $this->_columns->add(new Vps_Grid_Column('vps_pool'))
            ->setData(new Vps_Data_Table_Parent('Pool'));
        $this->_columns->add(new Vps_Grid_Column('vps_pool_id', trlVps('Pool'), 200))
            ->setEditor($select)
            ->setType('string')
            ->setShowDataIndex('vps_pool');
    }
}
