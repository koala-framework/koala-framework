<?php
class Vpc_Forum_User_View_Guestbook_Write_Component extends Vpc_Forum_Posts_Write_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['success'] = 'Vpc_Forum_User_View_Guestbook_Write_Success_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = Vpc_Posts_Write_Component::getTemplateVars();
        return $ret;
    }

    protected function _processForm()
    {
        Vpc_Posts_Write_Component::_processForm();

        $authedUser = Zend_Registry::get('userModel')->getAuthedUser();

        if (!isset($_POST['preview']) && isset($_POST['sbmt'])) {
            $userRow = Zend_Registry::get('userModel')->fetchRow(array(
                'id = ?' => $this->getParentComponent()->getCurrentPageKey()
            ));
            if ($userRow && $userRow->email && (!$authedUser || $userRow->id != $authedUser->id)) {
                $mail = new Vps_Mail('User/NewGuestbook');
                $mail->subject = trlVps('New entry in guestbook');
                $mail->addTo($userRow->email, $userRow->__toString());

                $mail->profileUrl = $this->getParentComponent()->getUrl();

                $values = $this->_getValues();
                $mail->content = $values['content'];
                $mail->fullname = $userRow->__toString();

                $mail->applicationName = Zend_Registry::get('config')->application->name;

                $mail->send();
            }
        } else {
            // wird in parentComponent in getTemplateVars gefangen.
            // ist leer damit kein fehler ausgegeben wird
            throw new Vps_ClientException();
        }
    }

    public function getThreadComponent()
    {
        return null;
    }

    public function getGroupComponent()
    {
        return null;
    }

    public function getForumComponent()
    {
        return $this->getParentComponent()->getForumComponent();
    }
}