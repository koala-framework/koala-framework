<?php
class Vpc_Mail_Redirect_Component extends Vpc_Abstract
{
    protected $_params = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Mail_Redirect_Model';
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        return $ret;
    }

    protected function _getRedirectRow()
    {
        if (!$this->_params || empty($this->_params['redirectId'])) {
            throw new Vps_Exception("params in object must be set before _getRedirectRow is called");
        }

        $r = $this->getModel()->getRow($this->_params['redirectId']);
        if (!$r) {
            throw new Vps_Exception("The redirect row was not found");
        }
        return $r;
    }

    public function processInput($inputData)
    {
        $params = explode('_', $inputData['d']);
        if (count($params) < 4) {
            throw new Vps_Exception("Too less parameters submitted");
        }

        $params = array(
            'redirectId' => $params[0],
            'recipientId' => $params[1],
            'recipientModelShortcut' => $params[2],
            'recipientModelClass' => $this->_getRecepientModelClass($params[2]),
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
            ->where(new Vps_Model_Select_Expr_HigherDate('click_date', date('Y-m-d H:i:s', time() - 10)));
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
            $recepientSource = self::getRecepientModelShortcut(
                $this->getData()->parent->componentClass,
                get_class($recipient->getModel())
            );

            $m = $this->getModel();
            $recipientPrimary = $recipient->getModel()->getPrimaryKey();
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

                // $recepientSource muss immer dabei sein, auch wenn es nur ein
                // model gibt. Würde später eines dazukommen, funktionierten die alten
                // Links nicht mehr

                // linkId_userId_userSource_hash
                $newLink = 'http://'.Vps_Registry::get('config')->server->domain
                    .$this->_getRedirectUrl(array($r->id, $recipient->$recipientPrimary, $recepientSource));
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

    public static function getRecepientModelShortcut($recepientSourcesComponentClass, $recipientModelClass)
    {
        $recipientSources = Vpc_Abstract::getSetting($recepientSourcesComponentClass, 'recepientSources');
        if (!in_array($recipientModelClass, $recipientSources)) {
            throw new Vps_Exception("'$recipientModelClass' is not set in setting 'recepientSources' in '$recepientSourcesComponentClass'");
        }

        $recepientSource = array_keys($recipientSources, $recipientModelClass);
        if (count($recepientSource) >= 2) {
            throw new Vps_Exception("'$recipientModelClass' exists ".count($recepientSource)." times in setting 'recepientSources' in '$recepientSourcesComponentClass'. It may only have 1 shortcut.");
        }

        return $recepientSource[0];
    }

    public static function getRecepientModelClass($recepientSourcesComponentClass, $recipientShortcut)
    {
        $recipientSources = Vpc_Abstract::getSetting($recepientSourcesComponentClass, 'recepientSources');
        if (!isset($recipientSources[$recipientShortcut])) {
            throw new Vps_Exception("Source key '$recipientShortcut' is not set in setting 'recepientSources' in '$recepientSourcesComponentClass'");
        }
        return $recipientSources[$recipientShortcut];
    }

    protected function _getRecepientModelClass($recipientShortcut)
    {
        return self::getRecepientModelClass(
            $this->getData()->parent->componentClass, $recipientShortcut
        );
    }

    private function _getHash(array $hashData)
    {
        $hashData = implode('', $hashData);
        return substr(md5($hashData.'3olu4tgfd9'), 0, 6);
    }
}
