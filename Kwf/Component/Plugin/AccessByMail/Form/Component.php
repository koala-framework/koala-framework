<?php
class Kwf_Component_Plugin_AccessByMail_Form_Component extends Kwc_Form_Component
{
    private $_accessByMailRow;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['subject'] = trlKwfStatic('Temporary access');
        $ret['generators']['child']['component']['success'] = 'Kwc_Form_Success_Component';
        return $ret;
    }

    public function processInput(array $postData)
    {
        parent::processInput($postData);
        $this->_accessByMailRow = false;
        if (isset($postData['key'])) {
            $s = new Kwf_Model_Select();
            $s->whereEquals('key', $postData['key']);
            $s->where(new Kwf_Model_Select_Expr_Higher('date', new Kwf_Date(time()-24*60*60)));
            $this->_accessByMailRow = Kwf_Model_Abstract::getInstance('Kwf_Component_Plugin_AccessByMail_Model')->getRow($s);
            if (!$this->_accessByMailRow) {
                $this->_errors[] = array(
                    'message' => trlKwf("Invalid or expired Link. Please request a new one.")
                );
            } else {
                $session = new Kwf_Session_Namespace('kwc_'.$this->getData()->parent->componentId);
                $session->login = true;
                $session->key = $postData['key'];
            }
        } else {
            $session = new Kwf_Session_Namespace('kwc_'.$this->getData()->parent->componentId);
            if ($session->login) {
                $s = new Kwf_Model_Select();
                $s->whereEquals('key', $session->key);
                $this->_accessByMailRow = Kwf_Model_Abstract::getInstance('Kwf_Component_Plugin_AccessByMail_Model')->getRow($s);
            }
        }
    }

    public function getAccessByMailRow()
    {
        if (is_null($this->_accessByMailRow)) {
            throw new Kwf_Exception('You must processInput first');
        }
        return $this->_accessByMailRow;
    }

    protected function _initForm()
    {
        $this->_form = new Kwf_Form();
        $this->_form->setModel(Kwf_Model_Abstract::getInstance('Kwf_Component_Plugin_AccessByMail_Model'));
        $this->_form->add(new Kwf_Form_Field_TextField('email', trlKwf('E-Mail')))
            ->setVtype('email')
            ->setAllowBlank(false);
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);

        $link = $this->getData()->url.'?key='.$row->key;

        $mail = new Kwf_Mail_Template($this->getData());
        $mail->addTo($row->email);
        $mail->subject = $this->_getPlaceholder('subject');
        $mail->link = 'http://'.Kwf_Registry::get('config')->server->domain.$link;
        $mail->send();
    }
}
