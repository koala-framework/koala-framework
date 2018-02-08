<?php
class Kwc_NewsletterCategory_Subscribe_ApiController extends Kwc_Newsletter_Subscribe_ApiController
{
    protected $_model = 'Kwc_NewsletterCategory_Subscribe_Model';

    protected function _insertSubscription(Kwf_Model_Row_Abstract $row)
    {
        //TODO: multiple categories
        if (!(int)$this->_getParam('categoryId')) {
            //parameter used in _afterInsertedSubscription
            throw new Kwf_Exception("parameter categoryId required");
        }
        return $this->_subscribe->getComponent()->insertSubscriptionWithCategory($row, (int)$this->_getParam('categoryId'));
    }

}
