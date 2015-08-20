<?php
class Kwc_Box_SwitchLanguage_Meta_Component extends Kwc_Abstract
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
        $ret['languages'] = $this->getData()->parent->getComponent()->getLanguages(true);
        return $ret;
    }

    public function injectIntoRenderedHtml($html)
    {
        $startPos = strpos($html, '<!-- alternate -->');
        $endPos = strpos($html, '<!-- /alternate -->')+19;
        return substr($html, 0, $startPos) .
            $this->getData()->render() .
            substr($html, $endPos);
    }
}
