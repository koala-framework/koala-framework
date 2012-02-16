<?php
class Kwc_Newsletter_ContentSender extends Kwf_Component_Abstract_ContentSender_Abstract
{
    public function sendContent($includeMaster)
    {
        throw new Kwf_Exception_NotFound();
    }
}
