<?php
class Vpc_Forum_LatestThreads_Feed_Component extends Vpc_Abstract_Feed_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'             => 'Vpc_Posts_Model'
        ));
        return $ret;
    }

    protected function _getRssEntries()
    {
        $ret = array();

        $pc = $this->getPageCollection();
        foreach ($this->getTable()->fetchAll(null, 'create_time DESC', 15) as $row) {
            $thread = $pc->getComponentById($row->component_id);
            if (!$thread) continue;
            $group = $thread->getGroupComponent();
            $ret[] = array(
                'title' => $group->getName(). ' - '.$thread->getName(),
                'link' => 'http://'.$_SERVER['HTTP_HOST'].$thread ->getUrl(),
                'description' => substr($row->content, 0, 55),
                'lastUpdate' => strtotime($row->create_time)
            );
        }
        return $ret;
    }

    protected function _getRssTitle()
    {
        return parent::_getRssTitle().' - '.trlVps('Forum Feed');
    }
}
