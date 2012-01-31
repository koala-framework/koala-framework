<?php
class Kwc_Newsletter_Detail_ContentSender extends Kwf_Component_Abstract_ContentSender_Default
{
    public function sendContent($includeMaster)
    {
        //show content only in preview, else a 404
        if (!Kwf_Component_Data_Root::getShowInvisible()) {
            throw new Kwf_Exception_NotFound();
        }
        parent::sendContent($includeMaster);
    }
}
