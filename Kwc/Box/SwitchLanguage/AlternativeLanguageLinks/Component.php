<?php
class Kwc_Box_SwitchLanguage_AlternativeLanguageLinks_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasInjectIntoRenderedHtml'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['languages'] = $this->getData()->parent->getComponent()->getLanguages(true, false);
        return $ret;
    }

    public function injectIntoRenderedHtml($html)
    {
        $startPos = strpos($html, '<!-- alternativeLanguageLinks -->');
        $endPos = strpos($html, '<!-- /alternativeLanguageLinks -->') + 34;
        return substr($html, 0, $startPos) .
            $this->getData()->render() .
            substr($html, $endPos);
    }
}
