<?php
class Vpc_Abstract_ListRandom_Component extends Vpc_Abstract_List_Component
    implements Vps_Component_Partial_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['limit'] = 1;
        $ret['partialClass'] = 'Vps_Component_Partial_Random';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = Vpc_Abstract::getTemplateVars();
        return $ret;
    }
    public function getPartialVars($partial, $nr, $info)
    {
        if (!$partial instanceof Vps_Component_Partial_Random)
            throw new Vps_Exception('Only Vps_Component_Partial_Random supported');
        $select = new Vps_Component_Select();
        $select->whereGenerator('child');
        $select->limit(1, $nr);
        return array('child' =>
            $this->getData()->getChildComponent($select)
        );
    }
    public function getPartialParams()
    {
        $ret = array();
        $ret['count'] = $this->getData()->countChildComponents(array('generator' => 'child'));
        $ret['limit'] = $this->_getSetting('limit');
        return $ret;
    }

    public function getPartialCacheVars($nr)
    {
        return array($this->_getCacheVars());
    }
}
