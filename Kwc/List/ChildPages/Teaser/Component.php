<?php
class Kwc_List_ChildPages_Teaser_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['child'] = array(
            'class' => 'Kwc_List_ChildPages_Teaser_Generator',
            'component' => 'Kwc_List_ChildPages_Teaser_TeaserImage_Component'
        );
        $ret['childModel'] = 'Kwc_List_ChildPages_Teaser_Model';

        $ret['componentName'] = trlKwfStatic('List child pages');
        $ret['componentCategory'] = 'childPages';
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['assetsAdmin']['dep'][] = 'KwfProxyPanel';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/List/List.js';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/List/ChildPages/Teaser/Panel.js';

        $ret['extConfig'] = 'Kwc_List_ChildPages_Teaser_ExtConfig';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['children'] = $this->getData()->getChildComponents(array('generator' => 'child'));
        return $ret;
    }
}
