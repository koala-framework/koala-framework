<?php
class Vps_Component_Cache_Chained_Master_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['childpages'] = array(
            'class' => 'Vps_Component_Cache_Chained_Master_Generator',
            'component' => 'Vps_Component_Cache_Chained_Master_Child_Component',
            'dbIdShortcut' => 'foo_'
        );
        $ret['childModel'] = 'Vps_Component_Cache_Chained_Master_ChildModel';
        $ret['ownModel'] = 'Vps_Component_Cache_Chained_Master_Model';
        $ret['flags']['chainedType'] = 'Trl';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['text'] = $this->getRow()->value;
        return $ret;
    }
}
