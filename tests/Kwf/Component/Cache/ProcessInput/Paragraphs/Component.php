<?php
class Kwf_Component_Cache_ProcessInput_Paragraphs_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['paragraphs']['component'] = array(
            'containsWithProcessInput' => 'Kwf_Component_Cache_ProcessInput_ContainsWithProcessInput_Component',
            'withProcessInput' => 'Kwf_Component_Cache_ProcessInput_WithProcessInput_Component',
        );
        $ret['childModel'] = 'Kwf_Component_Cache_ProcessInput_Paragraphs_TestModel';
        return $ret;
    }
}
