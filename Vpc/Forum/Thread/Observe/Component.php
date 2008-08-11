<?php
class Vpc_Forum_Thread_Observe_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['tablename'] = 'Vpc_Forum_Thread_Observe_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $user = Zend_Registry::get('userModel')->getAuthedUser();
        $ret['isObserved'] = false;
        $ret['userIsAuthed'] = false;
        
        if ($user) {
            $where = array(
                'thread_id = ?' => $this->getData()->getPage()->row->id,
                'user_id = ?' => $user->id
            );
            $observeRow = $this->getTable()->fetchRow($where);

            if ($this->_getParam('observe')) {
                if (!$observeRow) {
                    $observeRow = $this->getTable()->createRow();
                    $observeRow->thread_id = $this->getData()->getPage()->row->id;
                    $observeRow->user_id = $user->id;
                    $observeRow->save();
                } else {
                    $observeRow->delete();
                    $observeRow = null;
                }
            }
            if ($observeRow) $ret['isObserved'] = true;
            $ret['userIsAuthed'] = true;
        }

        $ret['observeUrl'] = $_SERVER['REQUEST_URI'];
        if (substr($ret['observeUrl'], -10) != '?observe=1') {
            $ret['observeUrl'] .= '?observe=1';
        }

        return $ret;
    }
    }