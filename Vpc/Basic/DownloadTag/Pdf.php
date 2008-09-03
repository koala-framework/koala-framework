<?php
class Vpc_Basic_DownloadTag_Pdf extends Vpc_Abstract_Pdf
{
    public function writeContent()
    {
        $vars = $this->_component->getTemplateVars();
        return $vars['url'];
    }

}
