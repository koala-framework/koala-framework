<?php
interface Kwf_Component_PluginRoot_Interface_MaskComponent
{
    public function getMaskCode(Kwf_Component_Data $page); // returns array('begin' => ..., 'end' => ...)
}
