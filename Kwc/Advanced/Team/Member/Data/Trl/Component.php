<?php
class Kwc_Advanced_Team_Member_Data_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponent = null)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['workingPosition'] = $this->_getRow()->working_position;
        $ret['vcard'] = $this->getData()->getChildComponent('_vcard');
        return $ret;
    }
}
