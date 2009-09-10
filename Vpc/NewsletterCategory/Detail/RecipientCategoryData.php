<?php
class Vpc_NewsletterCategory_Detail_RecipientCategoryData extends Vps_Data_Abstract
{
    protected $_poolId;

    public function __construct($poolId)
    {
        if (!is_numeric($poolId)) {
            throw new Vps_Exception("pool id must be set as a numeric value");
        }

        $this->_poolId = $poolId;
    }

    public function load($row)
    {
        $model = Vps_Model_Abstract::getInstance('Vpc_NewsletterCategory_Subscribe_SubscriberToPool');
        $hasCategory = $model->getRow($model->select()
            ->whereEquals('subscriber_id', $row->id)
            ->whereEquals('pool_id', $this->_poolId)
        );
        return ($hasCategory ? true : false);
    }
}
