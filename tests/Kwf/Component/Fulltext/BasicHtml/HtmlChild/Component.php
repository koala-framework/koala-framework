<?php
class Kwf_Component_Fulltext_BasicHtml_HtmlChild_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['html'] = 'Kwf_Component_Fulltext_BasicHtml_Html_Component';
        return $ret;
    }
}
