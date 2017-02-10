<?php
class Kwc_NewsletterCategory_Detail_RecipientCategoryData extends Kwf_Data_Abstract
{
    protected $_categoryId;

    public function __construct($categoryId)
    {
        if (!is_numeric($categoryId)) {
            throw new Kwf_Exception("category id must be set as a numeric value");
        }

        $this->_categoryId = $categoryId;
    }

    public function load($row, array $info = array())
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_NewsletterCategory_Subscribe_SubscriberToCategory');
        $hasCategory = $model->getRow($model->select()
            ->whereEquals('subscriber_id', $row->id)
            ->whereEquals('category_id', $this->_categoryId)
        );
        return ($hasCategory ? true : false);
    }
}
