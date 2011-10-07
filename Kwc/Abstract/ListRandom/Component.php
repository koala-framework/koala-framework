<?php
class Kwc_Abstract_ListRandom_Component extends Kwc_Abstract_List_Component
    implements Kwf_Component_Partial_Interface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['limit'] = 1;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = Kwc_Abstract::getTemplateVars();
        return $ret;
    }

    public function getPartialVars($partial, $nr, $info)
    {
        if (!$partial instanceof Kwf_Component_Partial_Random)
            throw new Kwf_Exception('Only Kwf_Component_Partial_Random supported');
        $select = new Kwf_Component_Select();
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

    public function getPartialClass()
    {
        return 'Kwf_Component_Partial_Random';
    }
}
