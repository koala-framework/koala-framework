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

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();
        $ret[] = new Vps_Component_Cache_Meta_Static_Model($row->getModel(), '{component_id}-moderate-close');
        return $ret;
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();
        $threadVars = $this->getData()->parent->getComponent()->getThreadVars();
        if ($threadVars['lastPost']->user) {
            $ret[] = new Vps_Component_Cache_Meta_Row($threadVars['lastPost']->user->row);
        }
        if ($threadVars['firstPost']->user) {
            $ret[] = new Vps_Component_Cache_Meta_Row($threadVars['firstPost']->user->row);
        }
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vpc_Posts_Directory_Model');
        return $ret;
    }
}
