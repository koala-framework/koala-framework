<?php
class Kwf_User_MessageRow extends Kwf_Model_Db_Row
{
    public function __toString()
    {
        if ($this->create_type == 'auto') {
            switch ($this->message_type) {
                case 'user_created':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Account created.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('Account created by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_edited':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Account edited.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('Account edited by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_role_changed':
                    $user = $this->getParentRow('User');
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Role changed to "{0}".', array($user->role));
                    } else {
                        $byUser = $this->getParentRow('ByUser');
                        $ret = trlKwf('Role changed to "{0}" by {1}.', array($user->role, $byUser->__toString()));
                    }
                    break;
                case 'user_activate':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Account activated.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('Account activated by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_password_set':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Password set.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('Password set by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_mail_UserDeleted':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Account deleted e-mail sent.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('Account deleted e-mail sent by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_mail_UserChangedMail':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Changed mail address e-mail sent.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('Changed mail address e-mail sent by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_mail_UserLostPassword':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Lost password e-mail sent.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('Lost password e-mail sent by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_mail_UserActivation':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Activation e-mail sent.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('Activation e-mail sent by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_mail_GlobalUserActivation':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Global user activation e-mail sent.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('Global user activation e-mail sent by {0}.', array($user->__toString()));
                    }
                    break;
                case 'user_deleted':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('User deleted.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('User deleted by {0}.', array($user->__toString()));
                    }
                    break;
                case 'wrong_login_password':
                    if (!$this->by_user_id) {
                        $ret = trlKwf('Wrong login password used.');
                    } else {
                        $user = $this->getParentRow('ByUser');
                        $ret = trlKwf('Wrong login password used by {0}.', array($user->__toString()));
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
        $this->by_user_id = Kwf_Registry::get('userModel')->getAuthedUserId();
        if (!$this->create_type) $this->create_type = 'auto';
    }
}
