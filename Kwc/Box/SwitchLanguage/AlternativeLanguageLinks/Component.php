<?php
class Kwc_Box_SwitchLanguage_AlternativeLanguageLinks_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasInjectIntoRenderedHtml'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
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
