<?php
class Kwc_NewsletterCategory_Subscribe_SubscriberToCategoryRow extends Kwf_Model_Db_Row
{
    protected function _beforeDelete()
    {
        parent::_beforeDelete();

        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        $subscriber = $this->getParentRow('Subscriber');
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($subscriber->newsletter_component_id, array('ignoreVisible' => true));
        if ($user) {
            $logMessage = $c->trlKwf('Removed from category {0} by {1}', array(
                $this->getParentRow('Category')->category,
                $user->name
            ));
        } else {
            $logMessage = $c->trlKwf('Removed from category {0}', array($this->getParentRow('Category')->category));
        }

        $subscriber->writeLog($logMessage, null, true);
    }

    protected function _beforeInsert()
    {
        parent::_beforeInsert();

        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        $subscriber = $this->getParentRow('Subscriber');
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($subscriber->newsletter_component_id, array('ignoreVisible' => true));
        if ($user) {
            $logMessage = $c->trlKwf('Added to category {0} by {1}', array(
                $this->getParentRow('Category')->category,
                $user->name
            ));
        } else {
            $logMessage = $c->trlKwf('Added to category {0}', array($this->getParentRow('Category')->category));
        }

        $subscriber->writeLog($logMessage, null, true);
    }
}

