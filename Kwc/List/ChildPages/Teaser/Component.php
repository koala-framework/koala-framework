<?php
class Kwc_List_ChildPages_Teaser_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['child'] = array(
            'class' => 'Kwc_List_ChildPages_Teaser_Generator',
            'component' => 'Kwc_List_ChildPages_Teaser_TeaserImage_Component'
        );
        $ret['childModel'] = 'Kwc_List_ChildPages_Teaser_Model';

        $ret['componentName'] = trlKwfStatic('List child pages');
        $ret['componentCategory'] = 'childPages';
        $ret['cssClass'] = 'webStandard';
        $ret['assetsAdmin']['dep'][] = 'KwfProxyPanel';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/List/List.js';

        $ret['extConfig'] = 'Kwc_List_ChildPages_Teaser_ExtConfig';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        /*
        Now the following line look unneded but I can tell you it is needed if you run koala
        on a 100 year old webserver with php 5.2.4 (WTF!) that crashes (yes, segfault) if the stack (or whatever?)
        is too large. Stupid PHP, Stupid POI. (Stupid is as stupid does.)
        */
        $this->getData()->getPage()->getChildPages();

        $ret['children'] = $this->getData()->getChildComponents(array('generator' => 'child'));
        return $ret;
    }
}
