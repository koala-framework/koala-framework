<?php
class Kwc_Advanced_SearchEngineReferer_ViewMyLatest_Component
    extends Kwc_Advanced_SearchEngineReferer_ViewLatest_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['header'] = trlKwfStatic('This site was latest found by').':';
        return $ret;
    }

    protected function _getSelect()
    {
        $select = parent::_getSelect();
        return $select->whereEquals('component_id', $this->getData()->parent->componentId);
    }
}
