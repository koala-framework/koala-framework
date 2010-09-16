<?php
class Vpc_NewsletterCategory_Subscribe_CategoriesModel extends Vps_Model_Db
{
    protected $_table = 'vpc_newsletter_subscribecategories';
    protected $_referenceMap = array(
        'Category' => array(
            'column' => 'category_id',
            'refModelClass' => 'Vpc_NewsletterCategory_CategoriesModel'
        )
    );

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
