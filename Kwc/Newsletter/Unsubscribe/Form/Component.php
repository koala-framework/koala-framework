<?php
class Kwc_Newsletter_Unsubscribe_Form_Component extends Kwc_Form_Component
{
    public $_recipient; //set by Kwc_Newsletter_Unsubscribe_Component

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] =
            'Kwc_Newsletter_Unsubscribe_Form_Success_Component';
        $ret['placeholder']['submitButton'] = trlKwfStatic('Unsubscribe newsletter');
        $ret['viewCache'] = false;
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        if ($this->_recipient) {
            $this->_form->setModel($this->_recipient->getModel());
            $this->_form->setId($this->_recipient->id);
        }
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        parent::_afterSave($row);
        $row->mailUnsubscribe();
    }

    //moved to FrontendForm
    protected final function getParentField()
    {}

    public function processInput(array $postData)
    {
        if (!$this->_recipient && isset($postData['d'])) {
            /**
             * 0 = redirectId
             * 1 = recipientId
             * 2 = recipientModelShortcut
             * 3 = hash
             */
            $params = explode('_', $postData['d']);
            if (count($params) >= 4 && $params[3] == $this->_getHash(array(
                    $params[0], $params[1], $params[2]
                ))) {
                $c = Kwf_Component_Data_Root::getInstance()->getComponentByClass('Kwc_Newsletter_Detail_Mail_Component', array(
                    'limit' => 1,
                    'subroot' => $this->getData()
                ));
                if ($c) {
                    $model = null;
                    foreach ($c->getComponent()->getRecipientSources() as $key=>$value) {
                        if (is_array($value) && $key == $params[2]) {
                            $model = $value['model'];
                        }
                    }
                    if ($model) {
                        $this->_recipient = Kwf_Model_Abstract::getInstance($model)
                            ->getRow($params[1]);
                    }
                }
            }
        }

        parent::processInput($postData);
    }

    private function _getHash(array $hashData)
    {
        $hashData = implode('', $hashData);
        return substr(Kwf_Util_Hash::hash($hashData), 0, 6);
    }
}
