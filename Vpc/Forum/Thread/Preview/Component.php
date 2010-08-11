<?php
class Vpc_Forum_Thread_Preview_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['thread'] = $this->getData()->parent;
        $ret = array_merge($ret, $ret['thread']->getComponent()->getThreadVars());
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $threadVars = $this->getData()->parent->getComponent()->getThreadVars();
        if ($threadVars['lastPost']->user) {
            $ret[] = array(
                'model' => Vps_Registry::get('config')->user->model,
                'id' => $threadVars['lastPost']->user->row->id
            );
        }
        if ($threadVars['firstPost']->user) {
            $ret[] = array(
                'model' => Vps_Registry::get('config')->user->model,
                'id' => $threadVars['firstPost']->user->row->id
            );
        }
        return $ret;
    }

    public static function getStaticCacheMeta()
    {
        $ret = parent::getStaticCacheMeta();
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vpc_Posts_Directory_Model');
        return $ret;
    }
}
