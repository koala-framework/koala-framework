<?php
abstract class Kwc_Feedback_Form_Component extends Kwc_Form_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Feedback');
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Grid';
        $ret['placeholder']['closeButtonText'] = trlKwfStatic('Cancel');
        return $ret;
    }

    public function processInput(array $postData)
    {
        parent::processInput($postData);
        if(!Kwf_Registry::get('userModel')->getAuthedUser())
            throw new Kwf_Exception('not logged in');
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);
        $row->component_id = $this->getData()->dbId;
        $row->user_id = Kwf_Registry::get('userModel')->getAuthedUser()->id;
        $row->date = date('Y-m-d H:i:s', time());
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->add(new Kwf_Form_Field_Static('<div class="footer"></div>'))    //makes the seperation(for styling) easier
            ->setCls('footer');
        $closeText = $this->_getPlaceholder('closeButtonText');
        $closeButton = '<a class="closeButton">' . $closeText . '</a><div class="kwfUp-clear"></div>';    //Cancel Button
        $this->_form->add(new Kwf_Form_Field_Static($closeButton))
            ->setCls('cancel');
    }

    // return an array with key email and name
    abstract protected function _getRecipient();

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_afterInsert($row);
        $pageName = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id)->getParentPage()->name;;
        $recipient = $this->_getRecipient();
        $user = $row->getParentRow('User');

        if ($recipient['email']) {
            $tpl = new Kwf_Mail_Template($this);
            $tpl->data = $this->getData();
            $tpl->text = $row->text;
            $tpl->pageName = $pageName;
            $tpl->user = $user;
            $tpl->addTo($recipient['email'], $recipient['name']);
            $tpl->setFrom($user->email, "$user->firstname $user->lastname");
            $tpl->setSubject($this->getData()->trlKwf('Feedback to page ') . $pageName);
            $tpl->send();
        }
    }
}
