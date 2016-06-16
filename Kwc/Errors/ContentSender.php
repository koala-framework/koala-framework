<?php
class Kwc_Errors_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function sendContent($includeMaster)
    {
        throw new Kwf_Exception_NotFound();
    }
}
