<?php
class Vpc_Forum_Posts_Observe_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['observeTableName'] = 'Vpc_Forum_Posts_Observe_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $tableName = $this->_getSetting('observeTableName');
        $t = new $tableName();
        $threadVars = $this->getThreadComponent()->getThreadVars();
        $userVars = Zend_Registry::get('userModel')->getAuthedUser();

        $ret['isObserved'] = false;
        $ret['userIsAuthed'] = $userVars ? true : false;

        if ($threadVars && $userVars) {
            // prüfen, dass nur einmal drin ist
            $where = array(
                'thread_id = ?' => $threadVars['thread_id'],
                'user_id = ?' => $userVars->id
            );
            $observeRow = $t->fetchRow($where);

            if ($this->_getParam('observe')) {
                if (!$observeRow) {
                    $observeRow = $t->createRow();
                    $observeRow->thread_id = $threadVars['thread_id'];
                    $observeRow->user_id = $userVars->id;
                    $observeRow->save();
                } else {
                    $observeRow->delete();
                    $observeRow = null;
                }
            }

            if ($observeRow) $ret['isObserved'] = true;
        }

        $ret['observeUrl'] = $_SERVER['REQUEST_URI'];
        if (substr($ret['observeUrl'], -10) != '?observe=1') {
            $ret['observeUrl'] .= '?observe=1';
        }

        return $ret;
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