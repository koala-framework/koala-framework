<?php
class Kwc_Guestbook_Write_Form_Component extends Kwc_Posts_Write_Form_Component
{
    protected function _getSettingsRow()
    {
        return $this->getData()->parent->getComponent()->getSettingsRow();
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);

        $settingsRow = $this->_getSettingsRow();

        if ($settingsRow->post_activation_type == Kwc_Guestbook_Component::INACTIVE_ON_SAVE) {
            $row->visible = 0;
        } else if ($settingsRow->post_activation_type == Kwc_Guestbook_Component::ACTIVE_ON_SAVE) {
            $row->visible = 1;
        }
    }

    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        $settingsRow = $this->_getSettingsRow();

        if ($settingsRow->new_post_mail) {
            $userRow = Kwf_Registry::get('userModel')->getRow($settingsRow->new_post_mail);
            if ($userRow) {
                $mailComponent = $this->getData()->parent->getComponent()->getInfoMailComponent();
                $guestbookComponent = $mailComponent->getParentByClass('Kwc_Guestbook_Component');

                if ($settingsRow->post_activation_type == Kwc_Guestbook_Component::INACTIVE_ON_SAVE) {
                    $activationChildId = 'activate';
                } else if ($settingsRow->post_activation_type == Kwc_Guestbook_Component::ACTIVE_ON_SAVE) {
                    $activationChildId = 'deactivate';
                }

                $mailComponent->getComponent()->send(
                    $userRow,
                    array(
                        'name'  => $row->name,
                        'email' => $row->email,
                        'url'   => $guestbookComponent->getAbsoluteUrl(),
                        'text'  => $row->content,
                        'activateComponent' => $guestbookComponent->getChildComponent("_$activationChildId"),
                        'activatePostId' => $row->id.'.'.Kwf_Util_Hash::hash($row->id),
                        'activationType' => $settingsRow->post_activation_type
                    ),
                    null,
                    Kwc_Mail_Recipient_Interface::MAIL_FORMAT_TEXT
                );
            }
        }
    }
}
