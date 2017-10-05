<?php
/**
 * Wird auch zum bearbeiten verwendet.
 * @see Kwc_Newsletter_Subscribe_Edit_Component
 */
class Kwc_Newsletter_Subscribe_Component extends Kwc_Form_Component
{
    const CONFIRM_MAIL_ONLY = 'confirm-mail-only';
    const DOUBLE_OPT_IN = 'double-opt-in';

    protected $_allowWriteLog = true;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Newsletter subscribing');
        $ret['placeholder']['submitButton'] = trlKwfStatic('Subscribe the newsletter');
        $ret['subscribeType'] = self::CONFIRM_MAIL_ONLY;

        $ret['generators']['mail'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Newsletter_Subscribe_Mail_Component'
        );
        $ret['generators']['doubleOptIn'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwc_Newsletter_Subscribe_DoubleOptIn_Component',
            'name' => trlKwfStatic('Opt In')
        );

        $ret['from'] = ''; // would be good if overwritten

        $ret['menuConfig'] = 'Kwc_Newsletter_Subscribe_MenuConfig';

        $ret['assetsAdmin']['dep'][] = 'KwfAutoGrid';
        $ret['assetsAdmin']['dep'][] = 'KwfProxyPanel';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Newsletter/Subscribe/RecipientsPanel.js';

        $ret['subscribeToNewsletterClass'] = 'Kwc_Newsletter_Component';
        return $ret;
    }

    public function getSubscribeToNewsletterComponent()
    {
        $nlData = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass($this->_getSetting('subscribeToNewsletterClass'), array('subroot'=>$this->getData()));
        if (!$nlData) {
            throw new Kwf_Exception('Cannot find newsletter component');
        }
        return $nlData;
    }

    public function insertSubscription(Kwc_Newsletter_Subscribe_Row $row)
    {
        $exists = $this->_subscriptionExists($row);
        if (!$exists) {
            $s = new Kwf_Model_Select();
            $s->whereEquals('email', $row->email);
            $s->whereEquals('newsletter_component_id', $this->getSubscribeToNewsletterComponent()->dbId);
            $s->where(new Kwf_Model_Select_Expr_Or(array(
                new Kwf_Model_Select_Expr_Equal('unsubscribed', 1),
                new Kwf_Model_Select_Expr_Equal('activated', 0)
            )));
            $deleteRow = $row->getModel()->getRow($s);
            if ($deleteRow) {
                $deleteRow->delete();
            }
            $this->_allowWriteLog = false;
            $this->_beforeInsert($row);
            $this->_allowWriteLog = true;
            $this->_writeLog($row);
            $row->save();
            $this->_afterInsert($row);
            return true;
        }
        return false;
    }

    protected function _subscriptionExists(Kwc_Newsletter_Subscribe_Row $row)
    {
        if ($row->id) {
            throw new Kwf_Exception("you can only insert unsaved rows");
        }
        $s = new Kwf_Model_Select();
        $s->whereEquals('email', $row->email); //what if the email field is not named email?
        $s->whereEquals('newsletter_component_id', $this->getSubscribeToNewsletterComponent()->dbId);
        $s->where(new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equal('unsubscribed', 1),
            new Kwf_Model_Select_Expr_Equal('activated', 1)
        )));

        if ($row->getModel()->countRows($s)) {
            //already subscribed, don't save
            return true;
        }
        return false;
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        if ($this->_getSetting('subscribeType') == self::CONFIRM_MAIL_ONLY) {
            $row->unsubscribed = 0;
            $row->activated = 1;
        } else if ($this->_getSetting('subscribeType') == self::DOUBLE_OPT_IN) {
            // set unsubscribed to not send him a newsletter until he
            // double-opted-in
            $row->unsubscribed = 0;
            $row->activated = 0;
        }
        $row->newsletter_component_id = $this->getSubscribeToNewsletterComponent()->dbId;

        if ($this->_allowWriteLog) {
            $row->setLogSource($this->getData()->getAbsoluteUrl());
            $this->_writeLog($row);
        }
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
        $editComponent = $nlData->getChildComponent('_editSubscriber');
        $unsubscribeComponent = null;
        $doubleOptInComponent = null;
        if ($this->_getSetting('subscribeType') == self::DOUBLE_OPT_IN) {
            $doubleOptInComponent = $this->getData()->getChildComponent('_doubleOptIn');
        } else {
            $unsubscribeComponent = $nlData->getChildComponent('_unsubscribe');
        }

        $mail = $this->getData()->getChildComponent('_mail')->getComponent();
        $mail->send($row, array(
            'formRow' => $row,
            'host' => $host,
            'unsubscribeComponent' => $unsubscribeComponent,
            'editComponent' => $editComponent,
            'doubleOptInComponent' => $doubleOptInComponent
        ));
    }

    protected function _initForm()
    {
        $formClass = Kwc_Admin::getComponentClass($this, 'FrontendForm');
        $this->_form = new $formClass(
            'form', $this->getData()->componentClass, $this->getData()->dbId
        );
    }

    protected function _writeLog(Kwf_Model_Row_Interface $row)
    {
        if ($this->_getSetting('subscribeType') == self::DOUBLE_OPT_IN) {
            $logMessage = $this->getData()->trlKwf('Subscribed');
            $state = 'subscribed';
        } else {
            $logMessage = $this->getData()->trlKwf('Subscribed and activated');
            $state = 'activated';
        }

        $row->writeLog($logMessage, $state);
    }
}
