<?php
class Kwc_Favourites_Box_Component extends Kwc_Abstract
{
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['favourite'] = Kwf_Component_Data_Root::getInstance()
            ->getComponentByClass('Kwc_Favourites_Page_Component', array('subroot' => $this->getData()));
        if (!$ret['favourite']) {
            throw new Kwf_Exception('Could not find "Kwc_Favourites_Page_Component"');
        }
        return $ret;
    }
}
