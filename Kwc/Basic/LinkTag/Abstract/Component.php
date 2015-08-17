<?php
abstract class Kwc_Basic_LinkTag_Abstract_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentIcon'] = 'page_link';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'url';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = array(
            'data' => $this->getData(),
            'cssClass' => self::getCssClass($this),
        );
        $ret['linkTitle'] = $ret['data']->getLinkTitle();
        return $ret;
    }

    public function hasContent()
    {
        if ($this->getData()->url) {
            return true;
        } else {
            return false;
        }
    }

}
