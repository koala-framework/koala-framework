<?php
class Kwc_Basic_DownloadTag_Pdf extends Kwc_Abstract_Pdf
{
    public function writeContent()
    {
        $vars = $this->_component->getTemplateVars(new Kwf_Component_Renderer());
        return $vars['url'];
    }

}
