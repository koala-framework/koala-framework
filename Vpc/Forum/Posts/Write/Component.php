<?php
class Vpc_Forum_Posts_Write_Component extends Vpc_Posts_Write_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['success'] = 'Vpc_Forum_Posts_Write_Success_Component';
        return $ret;
    }

    protected function _getInitContent()
    {
        if ($this->_getParam('quote')) {
            $postComponent = $this->getPageCollection()->getComponentById($this->_getParam('quote'));
            $initContent = $postComponent->getContent();

            $userComponent = null;
            foreach ($postComponent->getChildComponents() as $component) {
                if ($component instanceof Vpc_Forum_Posts_Post_UserDetail_Component) {
                    $userComponent = $component;
                    break;
                }
            }

            $uservars = null;
            if ($userComponent) {
                $uservars = $userComponent->getTemplateVars();
            }

            $userstr = '';
            if ($uservars) {
                $userstr = '='.$uservars['name'];
            }

            $initContent = '[quote'.$userstr.']'.$initContent.'[/quote]';
            return $initContent;
        } else {
            return parent::_getInitContent();
        }
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $t = $this->getThreadComponent();
        if ($t) {
            $ret['thread'] = $t->getName();
            $ret['threadUrl'] = $t->getUrl();
        } else {
            $ret['thread'] = null;
            $ret['threadUrl'] = null;
        }
        $ret['group'] = $this->getGroupComponent()->getName();
        $ret['groupUrl'] = $this->getGroupComponent()->getUrl();
        $ret['forum'] = $this->getForumComponent()->getName();
        $ret['forumUrl'] = $this->getForumComponent()->getUrl();
        return $ret;
    }

    protected function _processForm()
    {
        parent::_processForm();

        if (!isset($_POST['preview'])) {
            $threadComponent = $this->getThreadComponent();
            $threadVars = $threadComponent->getThreadVars();
            $observeTable = new Vpc_Forum_Posts_Observe_Model();
            $observers = $observeTable->fetchAll(array('thread_id' => $threadVars['thread_id']));

            foreach ($observers as $observer) {
                $userRow = Zend_Registry::get('userModel')->fetchAll(array(
                    'id = ?' => $observer->user_id
                ))->current();

                if ($userRow) {
                    $this->_sendObserveMail($userRow, $threadComponent, $threadVars);
                }
            }
        }
    }

    private function _sendObserveMail($userRow, $threadComponent, $threadVars)
    {
        $webUrl = 'http://'.$_SERVER['HTTP_HOST'];
        $host = parse_url($webUrl, PHP_URL_HOST);
        $hostNonWww = $host;
        if (substr($hostNonWww, 0, 4) == 'www.') {
            $hostNonWww = substr($hostNonWww, 4);
        }

        if (Zend_Registry::get('config')->email) {
            $fromName = Zend_Registry::get('config')->email->from->name;
            $fromAddress = Zend_Registry::get('config')->email->from->address;
        } else {
            $fromName = Zend_Registry::get('config')->application->name;
            $fromAddress = 'noreply@'.$hostNonWww;
        }

        $mailView = new Vps_View_Smarty();

        $mailView->webUrl = $webUrl;
        $mailView->fullname = $userRow->__toString();
        $mailView->userData = $userRow->toArray();
        $mailView->threadUrl = $threadComponent->getUrl();
        $mailView->threadName = $threadVars['subject'];
        $mailView->applicationName = Zend_Registry::get('config')->application->name;

        $mailView->setRenderFile('mails/ForumThreadObserve.txt.tpl');
        $bodyText = $mailView->render('mails/ForumThreadObserve.txt.tpl');

        $mailView->setRenderFile('mails/ForumThreadObserve.html.tpl');
        $bodyTextHtml = $mailView->render('mails/ForumThreadObserve.html.tpl');

        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($bodyTextHtml);
        $mail->setBodyText($bodyText);
        $mail->setFrom($fromAddress, $fromName);
        $mail->addTo($userRow->email, $userRow->__toString());
        $mail->setSubject(trlVps('New post in observed thread'));
        $mail->send();
    }

    public function getThreadComponent()
    {
        return $this->getParentComponent()->getParentComponent();
    }

    public function getGroupComponent()
    {
        return $this->getThreadComponent()->getGroupComponent();
    }

    public function getForumComponent()
    {
        return $this->getThreadComponent()->getForumComponent();
    }
}