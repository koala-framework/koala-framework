<?php
class Vpc_Forum_LatestThreads_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Forum.Latest Threads');
        $ret['tablename'] = 'Vpc_Forum_Group_Model';
        $ret['numberOfThreads'] = 5;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['threads'] = array();
        $rows = $this->getTable()->fetchAll(
            null, 'create_time DESC', $this->_getSetting('numberOfThreads')
        );
        foreach ($rows as $row) {
            $thread = Vps_Component_Data_Root::getInstance()->getComponentById($row->component_id . '_' . $row->id);
            foreach ($thread->getComponent()->getThreadVars() as $key => $val) {
                $thread->$key = $val;
            }
            $ret['threads'][] = $thread;
        }
        return $ret;
    }
}
