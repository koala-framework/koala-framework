<?php
class Kwc_Posts_Write_Form_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Kwc_Posts_Write_Form_Success_Component';
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setModel($this->getData()->parent->getComponent()->getPostsModel());
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        if ($row->getModel()->hasColumn('component_id')) {
            $row->component_id = $this->getData()->parent->getComponent()->getPostsDirectory()->dbId;
        }
        /*
        if (get_class($this) == 'Kwc_Posts_Write_Form_Component') {
            if ($this->getData()->parent->parent->getComponent() instanceof
                Kwc_User_Detail_Guestbook_Component)
            {
                $guestbook = $this->getData()->parent->parent;
                $userRow = $this->getData()->parent->parent->parent->row;

                $mail = new Kwf_Mail_Template($guestbook);
                $mail->subject = $this->getData()->trlKwf('New entry in your guestbook');
                $mail->addTo($userRow->email, $userRow->__toString());
                $mail->name = $userRow->nickname;
                $mail->url = 'http://' . $_SERVER['HTTP_HOST'] . $guestbook->getUrl();
                $mail->text = $row->content;
                $mail->send();
            }
        }
        */
    }
}
