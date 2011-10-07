<?php
class Vpc_Guestbook_Write_Form_Component extends Vpc_Posts_Write_Form_Component
{
    protected function _getSettingsRow()
    {
        return $this->_getPostsComponent()->getComponent()->getRow();
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        parent::_beforeInsert($row);

        $settingsRow = $this->_getSettingsRow();

        if ($settingsRow->post_activation_type == Vpc_Guestbook_Component::INACTIVE_ON_SAVE) {
            $row->visible = 0;
        } else if ($settingsRow->post_activation_type == Vpc_Guestbook_Component::ACTIVE_ON_SAVE) {
            $row->visible = 1;
        }
    }

    protected function _afterInsert(Vps_Model_Row_Interface $row)
    {
        $settingsRow = $this->_getSettingsRow();

        if ($settingsRow->new_post_mail) {
            $userRow = Vps_Registry::get('userModel')->getRow($settingsRow->new_post_mail);
            if ($userRow) {
                $guestbookComponent = $this->_getPostsComponent();
                $mailComponent = $guestbookComponent->getChildComponent('-mail');

                if ($settingsRow->post_activation_type == Vpc_Guestbook_Component::INACTIVE_ON_SAVE) {
                    $activationChildId = 'activate';
                } else if ($settingsRow->post_activation_type == Vpc_Guestbook_Component::ACTIVE_ON_SAVE) {
                    $activationChildId = 'deactivate';
                }

                $mailComponent->getComponent()->send(
                    $userRow,
                    array(
                        'name'  => $row->name,
                        'email' => $row->email,
                        'url'   => 'http://' . $_SERVER['HTTP_HOST'] . $guestbookComponent->getUrl(),
                        'text'  => $row->content,
                        'activateId' => $guestbookComponent->getChildComponent("-$activationChildId")->componentId,
                        'activatePostId' => $row->id,
                        'activationType' => $settingsRow->post_activation_type
                    ),
                    null,
                    Vpc_Mail_Recipient_Interface::MAIL_FORMAT_TEXT
                );
            }
        }
    }
}
