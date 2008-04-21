<?php
class Vpc_Posts_Post_UserDetail_Component extends Vpc_Abstract
{
    protected function _getUser()
    {
        $post = $this->getParentComponent()->getTable()
            ->find($this->getParentComponent()->getCurrentComponentKey())->current();
        return Zend_Registry::get('userModel')->find($post->user_id)->current();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $user = $this->_getUser();
        if ($user) {
            $ret['name'] = $user->firstname;
            $ret['created'] = $user->created;
        } else {
            $ret['name'] = 'Anonym';
            $ret['created'] = null;
        }
        return $ret;
    }
}
