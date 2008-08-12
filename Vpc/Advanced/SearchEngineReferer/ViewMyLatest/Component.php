<?php
class Vpc_Advanced_SearchEngineReferer_ViewMyLatest_Component
    extends Vpc_Advanced_SearchEngineReferer_ViewLatest_Component
{
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['component_id = ?'] = $this->getData()->parent->parent->dbId;
        return $ret;
    }

}
