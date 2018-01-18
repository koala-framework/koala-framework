<?php
class Kwc_Mail_Redirect_Component extends Kwc_Abstract
{
    protected $_params = array();
    protected $_redirectRow = null;
    protected $_redirectRowsCache = array();

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Mail_Redirect_Model';
        $ret['viewCache'] = false;
        $ret['flags']['processInput'] = true;
        $ret['flags']['skipFulltext'] = true;
        $ret['flags']['noIndex'] = true;
        $ret['contentSender'] = 'Kwc_Mail_Redirect_ContentSender';
        return $ret;
    }

    public static function parseRecipientParam($recipient)
    {
        $data = self::parseRecipientParamData($recipient);
        return $data['recipient'];
    }

    public static function parseRecipientParamData($recipient)
    {
        $parts = explode('.', $recipient);
        if (Kwf_Util_Hash::hash($parts[0].'.'.$parts[1].'.'.$parts[2]) != $parts[3]) {
            throw new Kwf_Exception_AccessDenied();
        }
        $redirectComponentId = $parts[0];
        $recipientModelShortcut = $parts[1];
        $recipientId = $parts[2];
        $redirectComponent = Kwf_Component_Data_Root::getInstance()->getComponentById($redirectComponentId);
        if (!$redirectComponent) throw new Kwf_Exception_NotFound();
        $m = Kwf_Model_Abstract::getInstance($redirectComponent->getComponent()->_getRecipientModelClass($recipientModelShortcut));
        $recipient = $m->getRow($recipientId);
        if (!$recipient) throw new Kwf_Exception_NotFound();
        return array(
            'recipient' => $recipient,
            'recipientId' => $recipientId,
            'recipientModelShortcut' => $recipientModelShortcut,
        );
    }

    //can be overridden to customize redirect url
    protected function _getRedirectUrl()
    {
        $r = $this->_getRedirectRow();

        if (isset($r->type) && $r->type && $r->type != 'redirect') {
            throw new Kwf_Exception_NotFound('Invalid type');
        }

        return $r->value;
    }

    public function getRedirectUrl()
    {
        $ret = $this->_getRedirectUrl();

        //forward all get parameters except d
        $get = $_GET;
        unset($get['d']);

        $cacheId = 'passMailReci-'.$ret;
        $passMailRecipient = Kwf_Cache_Simple::fetch($cacheId);
        if ($passMailRecipient === false) {
            $passMailRecipient = 0;
            if (substr($ret, 0, 1) == '/') {
                $p = Kwf_Component_Data_Root::getInstance()->getPageByUrl('http://'.$this->getData()->getDomain().$ret, null);
                if ($p) {
                    if (Kwc_Abstract::getFlag($p->componentClass, 'passMailRecipient')) {
                        $passMailRecipient = true;
                    }
                }
            }
            Kwf_Cache_Simple::add($cacheId, $passMailRecipient);
        }

        if ($passMailRecipient) {
            //internal page that also gets a prameter *might* need where they come from
            $get['recipient'] = $this->getData()->componentId.'.'.$this->_params['recipientModelShortcut'].'.'.$this->_params['recipientId'];
            $get['recipient'] .= '.'.Kwf_Util_Hash::hash($get['recipient']);
        }
        $get = http_build_query($get);
        $urlParts = parse_url($ret);
        $ret = (isset($urlParts['scheme']) ? "{$urlParts['scheme']}:" : '') .
            (isset($urlParts['host']) ? '//' : '') .
            (isset($urlParts['host']) ? "{$urlParts['host']}" : '') .
            (isset($urlParts['port']) ? ":{$urlParts['port']}" : '') .
            (isset($urlParts['path']) ? "{$urlParts['path']}" : '') .
            ($get ? "?$get" : '') .
            (isset($urlParts['fragment']) ? "#{$urlParts['fragment']}" : '');

        return $ret;
    }

    protected final function _getRedirectRow()
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
        if ($params['hash'] != $this->_createRedirectHash(array(
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
    }

    public function replaceLinks($mailText, Kwc_Mail_Recipient_Interface $recipient, $mode)
    {
        if ($mode == 'mailhtml' || $mode == 'html') {
            $that = $this;
            $mailText = preg_replace_callback('#(<a [^>]*href=")([^>"]+)()#', function($m) use ($that, $recipient) {
                $link = Kwf_Util_HtmlSpecialChars::decode($m[2]);
                $link = $that->_createRedirectUrl($link, $recipient);
                return $m[1].Kwf_Util_HtmlSpecialChars::filter($link).$m[3];
            }, $mailText);
        } else if ($mode == 'mailtext') {
            $that = $this;
            $mailText = preg_replace_callback('#https?://[^ \n]+#', function($m) use ($that, $recipient) {
                $link = $m[0];
                $link = $that->_createRedirectUrl($link, $recipient);
                return $link;
            }, $mailText);
        }

        return $mailText;
    }

    //should be protected but that doesn't allow calling it from closure
    //TODO: when upgrading to php 5.4 change to protected (and change $that in closure to $this)
    public function _createRedirectUrl($href, $recipient)
    {
        $recipientPrimary = $recipient->getModel()->getPrimaryKey();
        $recipientSource = $this->getRecipientModelShortcut(get_class($recipient->getModel()));

        $m = $this->getChildModel();

        if (substr($href, 0, 1) == '#') return $href;

        $hrefParts = parse_url($href);

        if (!isset($hrefParts['path'])) {
            $hrefParts['path'] = '';
        }
        $query = isset($hrefParts['query']) ? $hrefParts['query'] : null;

        $useAbsoluteUrl = true;
        if (isset($hrefParts['scheme'])) {
            if (($hrefParts['scheme'] == 'http' || $hrefParts['scheme'] == 'https') && isset($hrefParts['host'])) {
                if ($hrefParts['host'] == $this->getData()->getDomain()) {
                    $useAbsoluteUrl = false;
                }
            }
        } else if (!isset($hrefParts['scheme']) && !isset($hrefParts['host'])) {
            $useAbsoluteUrl = false;
        }
        if ($useAbsoluteUrl) {
            $link = (isset($hrefParts['scheme']) ? "{$hrefParts['scheme']}:" : '') .
                (isset($hrefParts['host']) ? '//' : '') .
                (isset($hrefParts['host']) ? "{$hrefParts['host']}" : '') .
                (isset($hrefParts['port']) ? ":{$hrefParts['port']}" : '') .
                (isset($hrefParts['path']) ? "{$hrefParts['path']}" : '') .
                (isset($hrefParts['fragment']) ? "#{$hrefParts['fragment']}" : '');
        } else {
            $link = $hrefParts['path'];
            if (!$link) $link = '/';
            if (isset($hrefParts['fragment'])) $link .= '#' . $hrefParts['fragment'];
        }

        if (!isset($this->_redirectRowsCache[$link])) {
            $select = $m->select();
            $select->whereEquals('value', $link);
            $r = $m->getRow($select);
            if (!$r) {
                $r = $m->createRow(array(
                    'value' => $link,
                ));
                $r->save();
            }
            $this->_redirectRowsCache[$link] = $r;
        }
        $r = $this->_redirectRowsCache[$link];

        // $recipientSource muss immer dabei sein, auch wenn es nur ein
        // model gibt. Würde später eines dazukommen, funktionierten die alten
        // Links nicht mehr

        // linkId_userId_userSource_hash
        return $this->_createHashedRedirectUrl(array(
            $r->id, $recipient->$recipientPrimary, $recipientSource
        )).($query ? '&'.$query : '');
    }

    protected function _createHashedRedirectUrl(array $parameters)
    {
        return $this->getData()->getAbsoluteUrl().'?d='
            .implode('_', $parameters)
            .'_'.$this->_createRedirectHash($parameters);
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

    private function _createRedirectHash(array $hashData)
    {
        $hashData = implode('', $hashData);
        return substr(Kwf_Util_Hash::hash($hashData), 0, 6);
    }
}
