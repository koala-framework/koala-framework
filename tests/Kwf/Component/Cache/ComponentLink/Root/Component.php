<?php
class Kwf_Component_Cache_ComponentLink_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_ComponentLink_Root_Model';
        $ret['generators']['page']['historyModel'] = new Kwf_Model_FnF();
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
        );
        unset($ret['generators']['title']);
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        for ($x = 1; $x <= 4; $x++) {
            $ret["p$x"] = Kwf_Component_Data_Root::getInstance()->getComponentById($x);
        }
        return $ret;
    }
}
