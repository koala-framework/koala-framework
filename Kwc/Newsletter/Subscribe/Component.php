<?php
/**
 * Wird auch zum bearbeiten verwendet.
 * @see Kwc_Newsletter_Subscribe_Edit_Component
 */
class Kwc_Newsletter_Subscribe_Component extends Kwc_Form_Component
{
    const CONFIRM_MAIL_ONLY = 'confirm-mail-only';
    const DOUBLE_OPT_IN = 'double-opt-in';

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Newsletter subscribing');
        $ret['placeholder']['submitButton'] = trlKwfStatic('Subscribe the newsletter');
        $ret['subscribeType'] = self::CONFIRM_MAIL_ONLY;
        $ret['flags']['hasResources'] = true;

        $ret['generators']['child']['component']['mail'] = 'Kwc_Newsletter_Subscribe_Mail_Component';
        $ret['generators']['child']['component']['doubleOptIn'] = 'Kwc_Newsletter_Subscribe_DoubleOptIn_Component';

        $ret['from'] = ''; // would be good if overwritten

        return $ret;
    }

    protected function _getSubscribeToNewsletterComponent()
    {
        $nlData = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_Newsletter_Component', array('subroot'=>$this->getData()));
        if (!$nlData) {
            throw new Kwf_Exception('Cannot find newsletter component');
        }
        return $nlData;
    }

    public function insertSubscription(Kwc_Newsletter_Subscribe_Row $row)
    {
        if ($row->id) {
            throw new Kwf_Exception("you can only insert unsaved rows");
        }
        $s = new Kwf_Model_Select();
        $s->whereEquals('email', $row->email); //what if the email field is not named email?

        $s->whereEquals('newsletter_component_id', $this->_getSubscribeToNewsletterComponent()->dbId);

        if ($row->getModel()->countRows($s)) {
            //already subscribed, don't save
            return false;
        }
        $this->_beforeInsert($row);
        $row->save();
        $this->_afterInsert($row);
        return true;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->subscribe_date = date('Y-m-d H:i:s');
        if ($this->_getSetting('subscribeType') == self::CONFIRM_MAIL_ONLY) {
            $row->unsubscribed = 0;
            $row->activated = 1;
        } else if ($this->_getSetting('subscribeType') == self::DOUBLE_OPT_IN) {
            // set unsubscribed to not send him a newsletter until he
            // double-opted-in
            $row->unsubscribed = 1;
            $row->activated = 0;
        }
        $row->newsletter_component_id = $this->_getSubscribeToNewsletterComponent()->dbId;
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);

        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Kwf_Registry::get('config')->server->domain;
        }

        $nlData = $this->getSubscribeToNewsletterComponent();
        $editComponentId = $nlData->getChildComponent('_editSubscriber')->componentId;

        $unsubscribeComponentId = null;
        $doubleOptInComponentId = null;
        if ($this->_getSetting('subscribeType') == self::DOUBLE_OPT_IN) {
            $doubleOptInComponentId = $this->getData()->getChildComponent('-doubleOptIn')->componentId;
        } else {
            $unsubscribeComponentId = $nlData->getChildComponent('_unsubscribe')->componentId;
        }

        $mail = $this->getData()->getChildComponent('-mail')->getComponent();
        $mail->send($row, array(
            'formRow' => $row,
            'host' => $host,
            'unsubscribeComponentId' => $unsubscribeComponentId,
            'editComponentId' => $editComponentId,
            'doubleOptInComponentId' => $doubleOptInComponentId
        ));
    }
}
