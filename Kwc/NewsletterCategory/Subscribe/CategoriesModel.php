<?php
class Kwc_NewsletterCategory_Subscribe_CategoriesModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_newsletter_subscribecategories';
    protected $_referenceMap = array(
        'Category' => array(
            'column' => 'category_id',
            'refModelClass' => 'Kwc_NewsletterCategory_CategoriesModel'
        )
    );

    protected function _setupFilters()
    {
        $filter = new Kwf_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
