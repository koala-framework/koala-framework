<?php
class Kwc_NewsletterCategory_Subscribe_ApiController extends Kwc_Newsletter_Subscribe_ApiController
{
    protected $_model = 'Kwc_NewsletterCategory_Subscribe_Model';

    public function jsonInsertAction()
    {
        //TODO: multiple categories
        if (!(int)$this->_getParam('categoryId')) {
            //parameter used in _afterInsertedSubscription
            throw new Kwf_Exception("parameter categoryId required");
        }
        parent::jsonInsertAction();
    }

    protected function _afterInsertedSubscription(Kwc_Newsletter_Subscribe_Row $row, $inserted)
    {
        $nl2cat = Kwf_Model_Abstract::getInstance('Kwc_NewsletterCategory_Subscribe_SubscriberToCategory');
        if (!$inserted) {
            //already subscribed
            $s = new Kwf_Model_Select();
            $s->whereEquals('email', $row->email);
            $s->whereEquals('newsletter_component_id', $this->_subscribe->getComponent()->getSubscribeToNewsletterComponent()->dbId);
            $row = $this->_model->getRow($s);

            $s = $nl2cat->select()
                ->whereEquals('subscriber_id', $row->id)
                ->whereEquals('category_id', (int)$this->_getParam('categoryId'));
            if ($nl2cat->countRows($s)) {
                //already subscribed to given category
                return;
            }
        }
        $nl2CatRow = $nl2cat->createRow();
        $nl2CatRow->subscriber_id = $row->id;
        $nl2CatRow->category_id = (int)$this->_getParam('categoryId');
        $nl2CatRow->save();
    }
}
