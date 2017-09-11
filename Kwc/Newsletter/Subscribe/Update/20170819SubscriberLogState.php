<?php
class Kwc_Newsletter_Subscribe_Update_20170819SubscriberLogState extends Kwf_Update
{
    public function postUpdate()
    {
        parent::postUpdate();

        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByClass('Kwc_Newsletter_Component') as $c) {
            $s = new Kwf_Model_Select();
            $s->whereEquals('newsletter_component_id', $c->dbId);
            $select = new Kwf_Model_Select();
            $select->where(new Kwf_Model_Select_Expr_Parent_Contains('Subscriber', $s));
            foreach (Kwf_Model_Abstract::getInstance('Kwc_Newsletter_Subscribe_LogsModel')->getRows($select) as $row) {
                if (stripos($row->message, $c->trlKwf('Activated')) !== false) {
                    $row->state = 'activated';
                } else if (stripos($row->message, $c->trlKwf('Unsubscribed')) !== false) {
                    $row->state = 'unsubscribed';
                } else if (stripos($row->message, $c->trlKwf('Subscribed')) !== false) {
                    $row->state = 'subscribed';
                }

                if ($row->isDirty('state')) $row->save();
            }
        }
    }
}
