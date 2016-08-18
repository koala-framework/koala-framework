<?php
class Kwc_Advanced_CommunityVideo_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'url';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        if ($this->getRow()->own_url) {
            $ret['url'] = Kwc_Advanced_CommunityVideo_Component::getVideoUrl($this->getRow()->url, $ret['row']);
        }
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getRow()->own_url) {
            if (Kwc_Advanced_CommunityVideo_Component::getVideoUrl($this->getRow()->url, $this->getData()->chained->getComponent()->getRow())) return true;

            return false;
        }

        return parent::hasContent();
    }
}
