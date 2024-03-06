<?php
class Kwf_User_MessageRow extends Kwf_Model_Db_Row
{
    private function getEditorUserNameIgnoreDeleted()
    {
        $userModel = $this->getModel()->getReferencedModel('ByUser');
        $s = new Kwf_Model_Select();
        $s->ignoreDeleted();
        $s->whereId($this->by_user_id);
        $user = $userModel->getRow($s);

        if ($user) {
            return $user . ($user->deleted ? ' (' . trlKwf('User deleted.') . ')' : '');
        } else {
            return trlKwf('User deleted.');
        }
    }

    public function __toString()
    {
        if ($this->create_type == 'auto') {
            switch ($this->message_type) {
                case 'user_created':
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('Account created by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('Account created by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('Account created by {0}.', $ip);
                    } else {
                        $ret = trlKwf('Account created.');
                    }
                    break;
                case 'user_edited':
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('Account edited by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('Account edited by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('Account edited by {0}.', $ip);
                    } else {
                        $ret = trlKwf('Account edited.');
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
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('Account activated by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('Account activated by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('Account activated by {0}.', $ip);
                    } else {
                        $ret = trlKwf('Account activated.');
                    }
                    break;
                case 'user_password_set':
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('Password set by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('Password set by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('Password set by {0}.', $ip);
                    } else {
                        $ret = trlKwf('Password set.');
                    }
                    break;
                case 'user_mail_UserDeleted':
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('Account deleted e-mail sent by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('Account deleted e-mail sent by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('Account deleted e-mail sent by {0}.', $ip);
                    } else {
                        $ret = trlKwf('Account deleted e-mail sent.');
                    }
                    break;
                case 'user_mail_UserChangedMail':
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('Changed mail address e-mail sent by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('Changed mail address e-mail sent by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('Changed mail address e-mail sent by {0}.', $ip);
                    } else {
                        $ret = trlKwf('Changed mail address e-mail sent.');
                    }
                    break;
                case 'user_mail_UserLostPassword':
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('Lost password e-mail sent by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('Lost password e-mail sent by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('Lost password e-mail sent by {0}.', $ip);
                    } else {
                        $ret = trlKwf('Lost password e-mail sent.');
                    }
                    break;
                case 'user_mail_UserActivation':
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('Activation e-mail sent by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('Activation e-mail sent by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('Activation e-mail sent by {0}.', $ip);
                    } else {
                        $ret = trlKwf('Activation e-mail sent.');
                    }
                    break;
                case 'user_mail_GlobalUserActivation':
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('Global user activation e-mail sent by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('Global user activation e-mail sent by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('Global user activation e-mail sent by {0}.', $ip);
                    } else {
                        $ret = trlKwf('Global user activation e-mail sent.');
                    }
                    break;
                case 'user_deleted':
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('User deleted by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('User deleted by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('User deleted by {0}.', $ip);
                    } else {
                        $ret = trlKwf('User deleted.');
                    }
                    break;
                case 'wrong_login_password':
                    if ($this->by_user_id) {
                        $username = $this->getEditorUserNameIgnoreDeleted();
                        $ret = trlKwf('Wrong login password used by {0}.', $username);
                    } else if ($this->ip === 'cli') {
                        $ret = trlKwf('Wrong login password used by {0}.', 'System');
                    } else if ($this->ip) {
                        $ip = preg_replace('/\.\d*\.\d*\./', '.*.*.', $this->ip);
                        $ret = trlKwf('Wrong login password used by {0}.', $ip);
                    } else {
                        $ret = trlKwf('Wrong login password used.');
                    }
                    break;
                case 'login':
                    $ret = trlKwf('Logged in.');
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
