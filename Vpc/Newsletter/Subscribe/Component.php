<?php
/**
 * Wird auch zum bearbeiten verwendet.
 * @see Vpc_Newsletter_Subscribe_Edit_Component
 */
class Vpc_Newsletter_Subscribe_Component extends Vpc_Form_Component
{
    const CONFIRM_MAIL_ONLY = 'confirm-mail-only';
    const DOUBLE_OPT_IN = 'double-opt-in';

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Newsletter subscribing');
        $ret['placeholder']['submitButton'] = trlVpsStatic('Subscribe the newsletter');
        $ret['subscribeType'] = self::CONFIRM_MAIL_ONLY;
        $ret['flags']['hasResources'] = true;

        $ret['generators']['child']['component']['mail'] = 'Vpc_Newsletter_Subscribe_Mail_Component';
        $ret['generators']['child']['component']['doubleOptIn'] = 'Vpc_Newsletter_Subscribe_DoubleOptIn_Component';

        $ret['from'] = ''; // would be good if overwritten

        return $ret;
    }

    public function insertSubscription(Vpc_Newsletter_Subscribe_Row $row)
    {
        if ($row->id) {
            throw new Vps_Exception("you can only insert unsaved rows");
        }
        $this->_beforeInsert($row);
        $row->save();
        $this->_afterInsert($row);
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
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
    }

    protected function _afterInsert(Vps_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);

        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Vps_Registry::get('config')->server->domain;
        }

        $nlData = Vps_Component_Data_Root::getInstance()
            ->getComponentByClass('Vpc_Newsletter_Component', array('subroot' => $this->getData()));
        if (!$nlData) {
            throw new Vps_Exception('Cannot find newsletter component');
        }
        $editComponentId = $nlData->getChildComponent('-editSubscriber')->componentId;
        $unsubscribeComponentId = null;
        $doubleOptInComponentId = null;
        if ($this->_getSetting('subscribeType') == self::DOUBLE_OPT_IN) {
            $doubleOptInComponentId = $this->getData()->getChildComponent('-doubleOptIn')->componentId;
        } else {
            $unsubscribeComponentId = $nlData->getChildComponent('-unsubscribe')->componentId;
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
