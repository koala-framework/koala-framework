<?php
class Kwc_Chained_CopyTarget_TargetGenerator extends Kwf_Component_Generator_Static
{
    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $ret['chained'] = $parentData->getComponent()->getTargetComponent();
        return $ret;
    }
}