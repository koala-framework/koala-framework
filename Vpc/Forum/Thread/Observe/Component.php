<?php
class Vpc_Forum_Thread_Observe_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Forum_Thread_Observe_Model';
        $ret['flags']['processInput'] = true;
        $ret['viewCache'] = false;
        return $ret;
    }

    public function processInput(array $postData)
    {
        $user = $this->_getUser();
        if ($user && isset($postData['observe'])) {
            $observeRow = $this->_getObserveRow();
            if (!$observeRow && $postData['observe']) {
                $observeRow = $this->getChildModel()->createRow();
                $observeRow->thread_id = $this->getData()->getPage()->row->id;
                $observeRow->user_id = $user->id;
                $observeRow->save();
            }
            if ($observeRow && !$postData['observe']) {
                $observeRow->delete();
            }
        }
    }

    protected function _getObserveRow()
    {
        if ($user = $this->_getUser()) {
            return $this->getChildModel()->getRow($this->getChildModel()->select()
                ->whereEquals('thread_id', $this->getData()->getPage()->row->id)
                ->whereEquals('user_id', $user->id)
            );
        }
        return null;
    }

    protected function _getUser()
    {
        return Zend_Registry::get('userModel')->getAuthedUser();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['isObserved'] = $this->_getObserveRow() != null;
        $ret['userIsAuthed'] = $this->_getUser() != null;
        $ret['observeUrl'] = $this->getData()->url;
        if (!$ret['isObserved']) {
            $ret['observeUrl'] .= '?observe=1';
        } else {
            $ret['observeUrl'] .= '?observe=0';
        }

        return $ret;
    }
}