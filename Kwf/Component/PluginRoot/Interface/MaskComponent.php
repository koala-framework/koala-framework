<?php
interface Kwf_Component_PluginRoot_Interface_MaskComponent
{
    const MASK_TYPE_NOMASK = 'noMask';
    const MASK_TYPE_HIDE = 'hide';
    const MASK_TYPE_SHOW = 'show';

    //returns a mask code for masking component in HTML
    public function getMaskCode(Kwf_Component_Data $page); // returns array('begin' => ..., 'end' => ...)

    //returns mask type and params, for usage in showMasked or getMaskCode
    public function getMask(Kwf_Component_Data $page); //returns array('type'=>..., 'params'=>...)

    //if true, all calls to showMasked can be ignored (skipped)
    public function canIgnoreMasks();

    //returns if a masked component (as returned by getMask) should be shown
    public function showMasked($maskType, $maskParams);
}
