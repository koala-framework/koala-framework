<?php
class Kwc_NewsletterCategory_Subscribe_Row extends Kwc_Newsletter_Subscribe_Row
{
    protected function _beforeDelete()
    {
        parent::_beforeDelete();

        $select = new Kwf_Model_Select();
        $select->whereEquals('subscriber_id', $this->id);
        $this->getModel()->getDependentModel('ToCategory')->deleteRows($select);
    }
}
