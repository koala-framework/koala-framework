<?php
class Kwc_Blog_Comments_Directory_Component extends Kwc_Posts_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['detail']['component'] = 'Kwc_Blog_Comments_Detail_Component';

        $ret['generators']['child']['component']['newCommentMail'] = 'Kwc_Blog_Comments_NewCommentMail_Component';

        unset($ret['generators']['write']);

        //either use write (on child page):
        //$ret['generators']['write']['component'] = 'Kwc_Blog_Comments_Write_Component';

        //or quickwrite (default):
        $ret['generators']['quickwrite'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Blog_Comments_QuickWrite_Component'
        );
        return $ret;
    }

    public function afterAddComment($row)
    {
        $blogSettingsRow = $this->getData()->getParentByClass('Kwc_Blog_Directory_Component')->getComponent()->getRow();

        if ($blogSettingsRow->new_comment_mail_recipient_user_id) {
            $userRow = Kwf_Registry::get('userModel')->getRow($blogSettingsRow->new_comment_mail_recipient_user_id);
            if ($userRow) {
                $mailComponent = $this->getData()->getChildComponent('-newCommentMail');

                $mailComponent->getComponent()->send(
                    $userRow,
                    array(
                        'name'  => $row->name,
                        'email' => $row->email,
                        'blogPost' => $this->getData()->getParentByClass('Kwc_Blog_Detail_Component'),
                        'url'   => $this->getData()->getAbsoluteUrl(),
                        'text'  => $row->content
                    ),
                    null,
                    Kwc_Mail_Recipient_Interface::MAIL_FORMAT_TEXT
                );
            }
        }
    }
}
