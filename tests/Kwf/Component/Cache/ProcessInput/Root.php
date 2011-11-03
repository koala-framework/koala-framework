<?php
class Kwf_Component_Cache_ProcessInput_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwf_Component_Cache_ProcessInput_PageTestModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_Empty_Component',
            'containsWithProcessInput' => 'Kwf_Component_Cache_ProcessInput_ContainsWithProcessInput_Component',
            'withProcessInput' => 'Kwf_Component_Cache_ProcessInput_WithProcessInput_Component',
            'paragraphs' => 'Kwf_Component_Cache_ProcessInput_Paragraphs_Component',
        );
        return $ret;
    }
}
