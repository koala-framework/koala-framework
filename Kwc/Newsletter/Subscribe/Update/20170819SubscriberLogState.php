<?php
class Kwc_Newsletter_Subscribe_Update_20170819SubscriberLogState extends Kwf_Update
{
    public function postUpdate()
    {
        parent::postUpdate();
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByClass('Kwc_Newsletter_Component') as $c) {
            $keywords = array(
                'subscribed' => $c->trlKwf('Subscribed'),
                'activated' => $c->trlKwf('Activated'),
                'unsubscribed' => $c->trlKwf('Unsubscribed'),
            );
            foreach ($keywords as $state => $keyword) {
                $query = "UPDATE kwc_newsletter_subscriber_logs l, kwc_newsletter_subscribers s set l.state='{$state}' WHERE l.subscriber_id=s.id AND s.newsletter_component_id='{$c->dbId}' AND LOCATE(LOWER('$keyword'), LOWER(message)) > 0";
                Kwf_Registry::get('db')->query($query);
            }
        }
    }
}
