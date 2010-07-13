<?php
class Vpc_NewsletterCategory_Detail_RecipientCategoryData extends Vps_Data_Abstract
{
    protected $_categoryId;

    public function __construct($categoryId)
    {
        if (!is_numeric($categoryId)) {
            throw new Vps_Exception("category id must be set as a numeric value");
        }

        $this->_categoryId = $categoryId;
    }

    public function load($row)
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_NewsletterCategory_Subscribe_SubscriberToCategory');
        $hasCategory = $model->getRow($model->select()
            ->whereEquals('subscriber_id', $row->id)
            ->whereEquals('category_id', $this->_categoryId)
        );
        return ($hasCategory ? true : false);
    }
}
