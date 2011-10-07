<?php
class Kwc_Basic_DownloadTag_Pdf extends Kwc_Abstract_Pdf
{
    public function writeContent()
    {
        $vars = $this->_component->getTemplateVars();
        return $vars['url'];
    }

}
