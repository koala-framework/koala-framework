<?php
class Vps_Component_Plugin_AccessByMail_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['subject'] = trlVpsStatic('Temporary access');
        $ret['generators']['child']['component']['success'] = 'Vpc_Form_Success_Component';
        return $ret;
    }

    public function processInput(array $postData)
    {
        parent::processInput($postData);
        if (isset($postData['key'])) {
            $s = new Vps_Model_Select();
            $s->whereEquals('key', $postData['key']);
            $s->where(new Vps_Model_Select_Expr_Higher('date', new Vps_Date(time()-24*60*60)));
            $row = Vps_Model_Abstract::getInstance('Vps_Component_Plugin_AccessByMail_Model')->getRow($s);
            if (!$row) {
                $this->_errors[] = trlVps("Invalid or expired Link. Please request a new one.");
            } else {
                $session = new Zend_Session_Namespace('vpc_'.$this->getData()->parent->componentId);
                $session->login = true;
            }
        }
    }

    protected function _initForm()
    {
        $this->_form = new Vps_Form();
        $this->_form->setModel(Vps_Model_Abstract::getInstance('Vps_Component_Plugin_AccessByMail_Model'));
        $this->_form->add(new Vps_Form_Field_TextField('email', trlVps('E-Mail')))
            ->setVtype('email')
            ->setAllowBlank(false);
    }

    protected function _afterInsert(Vps_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);

        $link = $this->getData()->url.'?key='.$row->key;

        $mail = new Vps_Mail_Template($this->getData());
        $mail->addTo($row->email);
        $mail->subject = $this->_getPlaceholder('subject');
        $mail->link = 'http://'.Vps_Registry::get('config')->server->domain.$link;
        $mail->send();
    }
}
