<?php
class Kwc_Abstract_ListRandom_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = Kwc_Chained_Trl_Component::getTemplateVars($renderer);
        return $ret;
    }

    //TODO getPartialVars
    //TODO getPartialParams
}
