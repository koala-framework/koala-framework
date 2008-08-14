<?php
class Vpc_Forum_LatestThreads_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Forum - latest threads');
        $ret['tablename'] = 'Vpc_Forum_Group_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['threads'] = array();
        foreach ($this->getTable()->fetchAll(null, null, 5) as $row) {
            $thread = Vps_Component_Data_Root::getInstance()->getComponentById($row->component_id . '_' . $row->id);
            foreach ($thread->getComponent()->getThreadVars() as $key => $val) {
                $thread->$key = $val;
            }
            $ret['threads'][] = $thread;
        }
        return $ret;
    }
}
