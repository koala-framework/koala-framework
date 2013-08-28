<?php
class Kwc_Mail_Redirect_Component extends Kwc_Abstract
{
    protected $_params = array();
    protected $_redirectRow = null;
    protected $_redirectRowsCache = array();

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Kwc_Mail_Redirect_Model';
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        $ret['contentSender'] = 'Kwc_Mail_Redirect_ContentSender';
        return $ret;
    }

    public final function getRedirectRow()
    {
        if (!$this->_redirectRow) {
            $this->_redirectRow = $this->_getRedirectRow();
        }
        return $this->_redirectRow;
    }

    protected function _getRedirectRow()
    {
        if (!$this->_params || empty($this->_params['redirectId'])) {
            throw new Kwf_Exception("params in object must be set before _getRedirectRow is called");
        }

        $r = $this->getChildModel()->getRow($this->_params['redirectId']);
        if (!$r) {
            throw new Kwf_Exception("The redirect row was not found");
        }
        return $r;
    }

    public function processInput($inputData)
    {
        if (empty($inputData['d'])) {
            throw new Kwf_Exception_NotFound();
        }

        $params = explode('_', $inputData['d']);
        if (count($params) < 4) {
            throw new Kwf_Exception_Client("Too few parameters submitted");
        }

        $modelClass = $this->_getRecipientModelClass($params[2]);
        if (!$modelClass) throw new Kwf_Exception_NotFound();
        $params = array(
            'redirectId' => $params[0],
            'recipientId' => $params[1],
            'recipientModelShortcut' => $params[2],
            'recipientModelClass' => $modelClass,
            'hash' => $params[3]
        );
        $this->_params = $params;

        // check the hash
        if ($params['hash'] != $this->_getHash(array(
            $params['redirectId'], $params['recipientId'], $params['recipientModelShortcut']
        ))) {
            throw new Kwf_Exception_Client("The submitted hash is incorrect.");
        }

        // statistics
        $statModel = Kwf_Model_Abstract::getInstance('Kwc_Mail_Redirect_StatisticsModel');
        // avoid double insert (e.g. click on link in kmail)
        $statSel = $statModel->select()
            ->whereEquals('mail_component_id', $this->getData()->parent->componentId)
            ->whereEquals('redirect_id', $params['redirectId'])
            ->whereEquals('recipient_id', $params['recipientId'])
            ->whereEquals('recipient_model_shortcut', $params['recipientModelShortcut'])
            ->where(new Kwf_Model_Select_Expr_Higher('click_date', new Kwf_DateTime(time() - 10)));
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

        $r = $this->getRedirectRow();
        if ($r->type == 'showcomponent') {
            $recipientRow = Kwf_Model_Abstract::getInstance($params['recipientModelClass'])
                ->getRow($params['recipientId']);
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($r->value)->getComponent();
            $c->processMailRedirectInput($recipientRow, $inputData);
        }
    }

    public function replaceLinks($mailText, Kwc_Mail_Recipient_Interface $recipient = null)
    {
        if ($recipient) {
            if ($recipient instanceof Zend_Db_Table_Row_Abstract) {
                $class = get_class($recipient->getTable());
                $recipientPrimary = $recipient->getTable()->info(Zend_Db_Table_Abstract::PRIMARY);
                $recipientPrimary = $recipientPrimary[1];
            } else if ($recipient instanceof Kwf_Model_Row_Abstract) {
                $class = get_class($recipient->getModel());
                $recipientPrimary = $recipient->getModel()->getPrimaryKey();
            } else {
                throw new Kwf_Exception('Only models or tables are supported.');
            }
            $recipientSource = $this->getRecipientModelShortcut($class);

            $m = $this->getChildModel();
        }

        while (preg_match('/\*([a-zA-Z_]+?)\*(.+?)(\*\*(.+?))?\*/', $mailText, $matches)) {
            if (!$recipient) {
                $mailText = str_replace(
                    $matches[0],
                    $matches[2],
                    $mailText
                );
            } else {
                $href = htmlspecialchars_decode($matches[2]);
                $title = '';
                if (isset($matches[4])) {
                    $title = htmlspecialchars_decode($matches[4]);
                }
                if (!isset($this->_redirectRowsCache[$href])) {
                    $r = $m->getRow($m->select()->whereEquals('value', $href));
                    if (!$r) {
                        $r = $m->createRow(array(
                            'value' => $href,
                            'type' => $matches[1]
                        ));
                        $r->save();
                    }
                    $this->_redirectRowsCache[$href] = $r;
                }
                $r = $this->_redirectRowsCache[$href];
                if (empty($r->title) && !empty($title)) {
                    $r->title = $title;
                    $r->save();
                }

                // $recipientSource muss immer dabei sein, auch wenn es nur ein
                // model gibt. Würde später eines dazukommen, funktionierten die alten
                // Links nicht mehr

                // linkId_userId_userSource_hash
                $newLink = $this->_getRedirectUrl(array(
                    $r->id, $recipient->$recipientPrimary, $recipientSource
                ));

                $mailText = str_replace($matches[0], $newLink, $mailText);
            }
        }
        return $mailText;
    }

    protected function _getRedirectUrl(array $parameters)
    {
        return $this->getData()->getAbsoluteUrl().'?d='
            .implode('_', $parameters)
            .'_'.$this->_getHash($parameters);
    }

    public function getRecipientModelShortcut($recipientModelClass)
    {
        $recipientSources = $this->getData()->parent->getComponent()->getRecipientSources();
        foreach ($recipientSources as $key=>$value) {
            if (is_array($value)) {
                $recipientSources[$key] = $value['model'];
            }
        }

        if (!in_array($recipientModelClass, $recipientSources)) {
            throw new Kwf_Exception("'$recipientModelClass' is not set in setting 'recipientSources' in '{$this->getData()->parent->componentClass}'");
        }

        $recipientSource = array_keys($recipientSources, $recipientModelClass);
        if (count($recipientSource) >= 2) {
            throw new Kwf_Exception("'$recipientModelClass' exists ".count($recipientSource)." times in setting 'recipientSources' in '{$this->getData()->parent->componentClass}'. It may only have 1 shortcut.");
        }

        return $recipientSource[0];
    }

    protected function _getRecipientModelClass($recipientShortcut)
    {
        $recipientSources = $this->getData()->parent->getComponent()->getRecipientSources();
        foreach ($recipientSources as $key=>$value) {
            if (is_array($value)) {
                $recipientSources[$key] = $value['model'];
            }
        }
        if (!isset($recipientSources[$recipientShortcut])) {
            return null;
        }
        return $recipientSources[$recipientShortcut];
    }

    private function _getHash(array $hashData)
    {
        $hashData = implode('', $hashData);
        return substr(Kwf_Util_Hash::hash($hashData), 0, 6);
    }
}
