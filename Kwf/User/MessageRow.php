<?php
class Vps_User_MessageRow extends Vps_Model_Db_Row
{
    public function __toString()
    {
        if ($this->create_type == 'auto') {
            switch ($this->message_type) {
                case 'user_created':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Account created.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('Account created by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_edited':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Account edited.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('Account edited by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_activate':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Account activated.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('Account activated by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_password_set':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Password set.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('Password set by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_mail_UserDeleted':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Account deleted e-mail sent.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('Account deleted e-mail sent by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_mail_UserChangedMail':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Changed mail address e-mail sent.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('Changed mail address e-mail sent by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_mail_UserLostPassword':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Lost password e-mail sent.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('Lost password e-mail sent by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_mail_UserActivation':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Activation e-mail sent.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('Activation e-mail sent by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_mail_GlobalUserActivation':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Global user activation e-mail sent.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('Global user activation e-mail sent by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_locked':
                    if (!$this->by_user_id) {
                        $ret = trlVps('User locked.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('User locked by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_unlocked':
                    if (!$this->by_user_id) {
                        $ret = trlVps('User unlocked.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('User unlocked by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_deleted':
                    if (!$this->by_user_id) {
                        $ret = trlVps('User deleted.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('User deleted by {0}.', array($user->__toString()));
                    }
                    break;
                case 'wrong_login_password':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Wrong login password used.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('Wrong login password used by {0}.', array($user->__toString()));
                    }
                    break;
                case 'wrong_login_locked':
                    if (!$this->by_user_id) {
                        $ret = trlVps('Tried login into locked account.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlVps('{0} tried to login into locked account.', array($user->__toString()));
                    }
                    break;
                default:
                    $ret = $this->message_type;
            }
            return $ret;
        } else if ($this->create_type == 'manual') {
            return $this->message;
        }
    }

    protected function _beforeInsert()
    {
        $userModel = Vps_Registry::get('userModel');
        $authedUser = $userModel->getAuthedUser();
        if ($authedUser && $authedUser->id) {
            $this->by_user_id = $authedUser->id;
        }
        if (!$this->create_type) $this->create_type = 'auto';
    }
}
