<?php
interface Kwf_Component_PluginRoot_Interface_PostRender
{
    public function processOutput($output);

    //if returns true processUrl doesn't have to be called
    public function canIgnoreProcessUrl();

    //process all urls before output
    //NOT called for HTML output (where processOutput should be used)
    //called for API requests like Menu_Mobile
    public function processUrl($url);
}
