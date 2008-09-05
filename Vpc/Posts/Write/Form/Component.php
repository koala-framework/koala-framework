<?php
class Vpc_Posts_Write_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Posts_Write_Form_Success_Component';
        $ret['tablename'] = 'Vpc_Posts_Directory_Model';
        $ret['cssClass'] .= ' vpsFormLabelAbove';
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
            $thread = $this->getData()->getParentPage();
            $observe = $thread->getChildComponent('-observe');
            $authedUser = Zend_Registry::get('userModel')->getAuthedUser();
            $table = Vpc_Abstract::createTable($observe->componentClass);
            $observers = $table->fetchAll(array('thread_id = ?' => $thread->row->id));
            foreach ($observers as $observer) {
                if ($authedUser && $authedUser->id == $observer->user_id) {
                    continue;
                }

                $userRow = Zend_Registry::get('userModel')->fetchAll(array(
                    'id = ?' => $observer->user_id
                ))->current();

                if ($userRow) {
                    $this->_sendObserveMail($userRow, $thread, $observe);
                }
            }
        }
    }
    
    private function _sendObserveMail($userRow, $thread, $observe)
    {
        $tpl = Vpc_Admin::getComponentFile($observe->componentClass, 'Component', 'html.tpl');
        $tpl = str_replace('.html.tpl', '', $tpl);
        $mail = new Vps_Mail($tpl);
        $mail->subject = trlVps('New post in observed thread');
        $mail->addTo($userRow->email, $userRow->__toString());
        $mail->threadUrl = 'http://' . $_SERVER['HTTP_HOST'] . $thread->getUrl();
        $mail->threadName = $thread->row->subject;
        return $mail->send();
    }
}
