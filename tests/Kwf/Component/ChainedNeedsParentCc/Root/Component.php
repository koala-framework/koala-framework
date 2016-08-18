<?php
class Kwf_Component_ChainedNeedsParentCc_Root_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['master'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_ChainedNeedsParentCc_Master_Component',
        );
        $ret['generators']['slave'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_ChainedNeedsParentCc_Chained_Component.Kwf_Component_ChainedNeedsParentCc_Master_Component',
        );
        return $ret;
    }
}
