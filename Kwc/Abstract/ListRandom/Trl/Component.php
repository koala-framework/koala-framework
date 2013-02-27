<?php
class Kwc_Abstract_ListRandom_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public function getTemplateVars()
    {
        $ret = Kwc_Chained_Trl_Component::getTemplateVars();
        return $ret;
    }

    //TODO getPartialVars
    //TODO getPartialParams
}
