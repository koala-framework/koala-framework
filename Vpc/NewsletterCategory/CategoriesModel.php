<?php
class Vpc_NewsletterCategory_CategoriesModel extends Vps_Model_Db
{
    protected $_table = 'vpc_newsletter_categories';
    protected $_referenceMap = array(
        'Pool' => array(
            'column' => 'vps_pool_id',
            'refModelClass' => 'Vps_Util_Model_Pool'
        )
    );

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $filter->setGroupBy('component_id');
        $this->_filters = array('pos' => $filter);
    }
}
