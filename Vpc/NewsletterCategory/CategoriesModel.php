<?php
class Vpc_NewsletterCategory_CategoriesModel extends Vps_Model_Db
{
    protected $_table = 'vpc_newsletter_categories';
    protected $_toStringField = 'category';

    protected function _setupFilters()
    {
        $filter = new Vps_Filter_Row_Numberize();
        $this->_filters = array('pos' => $filter);
    }
}
