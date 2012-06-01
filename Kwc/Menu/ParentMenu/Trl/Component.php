<?php
class Kwc_Menu_ParentMenu_Trl_Component extends Kwc_Menu_Trl_Component
{
    public function hasContent()
    {
        $tvars = $this->getTemplateVars();
        $c = count($tvars['menu']);
        
        if (Kwc_Abstract::getSetting(Kwc_Abstract::getSetting($this->getData()->chained->componentClass, 'menuComponentClass'), 'emptyIfSingleEntry')) {
            if ($c > 1) return true;
        } else if ($c > 0) {
            return true;
        }
        return false;
    }
}
