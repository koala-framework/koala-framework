<?php
class Kwc_Box_MetaTags_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['flags']['hasInjectIntoRenderedHtml'] = true;
        return $ret;
    }

    public function injectIntoRenderedHtml($html)
    {
        return Kwc_Box_MetaTags_Component::injectMeta($html, $this->getData()->render());
    }
}
