<?php
class Kwf_Component_Cache_Chained_Master_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['childpages'] = array(
            'class' => 'Kwf_Component_Cache_Chained_Master_Generator',
            'component' => 'Kwf_Component_Cache_Chained_Master_Child_Component',
            'dbIdShortcut' => 'foo_'
        );
        $ret['childModel'] = 'Kwf_Component_Cache_Chained_Master_ChildModel';
        $ret['ownModel'] = 'Kwf_Component_Cache_Chained_Master_Model';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['text'] = $this->getRow()->value;
        return $ret;
    }
}
