<?php
class Vpc_Basic_LinkTag_Event_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_Event_Data';
        $ret['componentName'] = trlVps('Link.to Event');
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        //ein event von der der status geändert wird oder der titel geändert wird
        if ($this->getData()->getLinkedData()) {
            $ret = array_merge($ret, $this->getData()->getLinkedData()
                                                ->getComponent()->getCacheVars());
        }
        return $ret;
    }
}
