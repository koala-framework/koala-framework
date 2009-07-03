<?php
class Vpc_Posts_Write_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Posts_Write_Form_Success_Component';
        $ret['tablename'] = 'Vpc_Posts_Directory_Model';
        return $ret;
    }

    protected function _getPostsComponent()
    {
        return $this->getData()->parent->parent;
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row)
    {
        $row->component_id = $this->_getPostsComponent()->dbId;
        if (get_class($this) == 'Vpc_Posts_Write_Form_Component') {
            if ($this->getData()->parent->parent->getComponent() instanceof 
                Vpc_User_Detail_Guestbook_Component)
            {
                $guestbook = $this->getData()->parent->parent;
                $userRow = $this->getData()->parent->parent->parent->row;

                $mail = new Vps_Mail_Template($guestbook);
                $mail->subject = trlVps('New entry in your guestbook');
                $mail->addTo($userRow->email, $userRow->__toString());
                $mail->name = $userRow->nickname;
                $mail->url = 'http://' . $_SERVER['HTTP_HOST'] . $guestbook->getUrl();
                $mail->text = $row->content;
                $mail->send();
            } else if ($this->getData()->getParentPage()->getChildComponent('-observe')) {
                $thread = $this->getData()->getParentPage();
                $observe = $thread->getChildComponent('-observe');
                $authedUser = Zend_Registry::get('userModel')->getAuthedUser();
                $table = Vpc_Abstract::createTable($observe->componentClass);
                $observers = $table->fetchAll(array('thread_id = ?' => $thread->row->id));
                $userModel = Zend_Registry::get('userModel');
                foreach ($observers as $observer) {
                    if ($authedUser && $authedUser->id == $observer->user_id) {
                        continue;
                    }

                    $userRow = $userModel->getRow($userModel->select()->whereEquals('id', $observer->user_id));
                    if ($userRow) {
                        $this->_sendObserveMail($userRow, $thread, $observe);
                    }
                }
            }
        }
    }

    private function _sendObserveMail($userRow, $thread, $observe)
    {
        $mail = new Vps_Mail_Template($observe);
        $mail->subject = trlVps('New post in observed thread');
        $mail->addTo($userRow->email, $userRow->__toString());
        $mail->threadUrl = 'http://' . $_SERVER['HTTP_HOST'] . $thread->getUrl();
        $mail->threadName = $thread->row->subject;
        return $mail->send();
    }
}
