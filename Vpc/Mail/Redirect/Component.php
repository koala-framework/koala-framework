<?php
class Vpc_Mail_Redirect_Component extends Vpc_Abstract
{
    protected $_params = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Mail_Redirect_Model';
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    protected function _getRedirectRow()
    {
        if (!$this->_params || empty($this->_params['redirectId'])) {
            throw new Vps_Exception("params in object must be set before _getRedirectRow is called");
        }

        $r = $this->getChildModel()->getRow($this->_params['redirectId']);
        if (!$r) {
            throw new Vps_Exception("The redirect row was not found");
        }
        return $r;
    }

    public function processInput($inputData)
    {
        if (empty($inputData['d'])) {
            throw new Vps_Exception_NotFound();
        }

        $params = explode('_', $inputData['d']);
        if (count($params) < 4) {
            throw new Vps_Exception("Too less parameters submitted");
        }

        $params = array(
            'redirectId' => $params[0],
            'recipientId' => $params[1],
            'recipientModelShortcut' => $params[2],
            'recipientModelClass' => $this->_getRecipientModelClass($params[2]),
            'hash' => $params[3]
        );
        $this->_params = $params;

        // check the hash
        if ($params['hash'] != $this->_getHash(array(
            $params['redirectId'], $params['recipientId'], $params['recipientModelShortcut']
        ))) {
            throw new Vps_Exception("The submitted hash is incorrect.");
        }

        // statistics
        $statModel = Vps_Model_Abstract::getInstance('Vpc_Mail_Redirect_StatisticsModel');
        // avoid double insert (e.g. click on link in kmail)
        $statSel = $statModel->select()
            ->whereEquals('mail_component_id', $this->getData()->parent->componentId)
            ->whereEquals('redirect_id', $params['redirectId'])
            ->whereEquals('recipient_id', $params['recipientId'])
            ->whereEquals('recipient_model_shortcut', $params['recipientModelShortcut'])
            ->where(new Vps_Model_Select_Expr_Higher('click_date', new Vps_DateTime(time() - 10)));
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $statSel->whereEquals('ip', $_SERVER['REMOTE_ADDR']);
        }
        if (!$statModel->getRow($statSel)) {
            $statRow = $statModel->createRow(array(
                'mail_component_id' => $this->getData()->parent->componentId,
                'redirect_id' => $params['redirectId'],
                'recipient_id' => $params['recipientId'],
                'recipient_model_shortcut' => $params['recipientModelShortcut'],
                'click_date' => date('Y-m-d H:i:s')
            ));
            if (isset($_SERVER['REMOTE_ADDR'])) $statRow->ip = $_SERVER['REMOTE_ADDR'];
            $statRow->save();
        }

        // if it is of type redirect, do the redirect
        $r = $this->_getRedirectRow();
        if ($r->type == 'redirect') {
            header('Location: '.$r->value);
            exit;
        } else if ($r->type == 'showcomponent') {
            $recipientRow = Vps_Model_Abstract::getInstance($params['recipientModelClass'])
                ->getRow($params['recipientId']);
            $c = Vps_Component_Data_Root::getInstance()
                ->getComponentById($r->value)->getComponent();
            $c->processMailRedirectInput($recipientRow, $inputData);
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $r = $this->_getRedirectRow();
        if ($r->type == 'showcomponent') {
            $ret['showcomponent'] = Vps_Component_Data_Root::getInstance()
                ->getComponentById($r->value);
        }
        return $ret;
    }


    public function replaceLinks($mailText, Vpc_Mail_Recipient_Interface $recipient = null)
    {
        if ($recipient) {
            if ($recipient instanceof Zend_Db_Table_Row_Abstract) {
                $class = get_class($recipient->getTable());
                $recipientPrimary = $recipient->getTable()->info(Zend_Db_Table_Abstract::PRIMARY);
                $recipientPrimary = $recipientPrimary[1];
            } else if ($recipient instanceof Vps_Model_Row_Abstract) {
                $class = get_class($recipient->getModel());
                $recipientPrimary = $recipient->getModel()->getPrimaryKey();
            } else {
                throw new Vps_Exception('Only models or tables are supported.');
            }
            $recipientSource = self::getRecipientModelShortcut(
                $this->getData()->parent->componentClass,
                $class
            );

            $m = $this->getChildModel();
        }

        while (preg_match('/\*(.+?)\*(.+?)\*/', $mailText, $matches)) {
            if (!$recipient) {
                $mailText = str_replace(
                    $matches[0],
                    'http://'.Vps_Registry::get('config')->server->domain.$matches[2],
                    $mailText
                );
            } else {
                $r = $m->getRow($m->select()->whereEquals('value', $matches[2]));
                if (!$r) {
                    $r = $m->createRow(array(
                        'value' => $matches[2],
                        'type' => $matches[1]
                    ));
                    $r->save();
                }

                // $recipientSource muss immer dabei sein, auch wenn es nur ein
                // model gibt. Würde später eines dazukommen, funktionierten die alten
                // Links nicht mehr

                // linkId_userId_userSource_hash
                $newLink = 'http://'.Vps_Registry::get('config')->server->domain
                    .$this->_getRedirectUrl(array($r->id, $recipient->$recipientPrimary, $recipientSource));
                $mailText = str_replace($matches[0], $newLink, $mailText);
            }
        }
        return $mailText;
    }

    protected function _getRedirectUrl(array $parameters)
    {
        return $this->getData()->getUrl().'?d='
            .implode('_', $parameters)
            .'_'.$this->_getHash($parameters);
    }

    public static function getRecipientModelShortcut($recipientSourcesComponentClass, $recipientModelClass)
    {
        $recipientSources = Vpc_Abstract::getSetting($recipientSourcesComponentClass, 'recipientSources');
        if (!in_array($recipientModelClass, $recipientSources)) {
            throw new Vps_Exception("'$recipientModelClass' is not set in setting 'recipientSources' in '$recipientSourcesComponentClass'");
        }

        $recipientSource = array_keys($recipientSources, $recipientModelClass);
        if (count($recipientSource) >= 2) {
            throw new Vps_Exception("'$recipientModelClass' exists ".count($recipientSource)." times in setting 'recipientSources' in '$recipientSourcesComponentClass'. It may only have 1 shortcut.");
        }

        return $recipientSource[0];
    }

    public static function getRecipientModelClass($recipientSourcesComponentClass, $recipientShortcut)
    {
        $recipientSources = Vpc_Abstract::getSetting($recipientSourcesComponentClass, 'recipientSources');
        if (!isset($recipientSources[$recipientShortcut])) {
            throw new Vps_Exception("Source key '$recipientShortcut' is not set in setting 'recipientSources' in '$recipientSourcesComponentClass'");
        }
        return $recipientSources[$recipientShortcut];
    }

    protected function _getRecipientModelClass($recipientShortcut)
    {
        return self::getRecipientModelClass(
            $this->getData()->parent->componentClass, $recipientShortcut
        );
    }

    private function _getHash(array $hashData)
    {
        $hashData = implode('', $hashData);
        return substr(Vps_Util_Hash::hash($hashData), 0, 6);
    }
}
