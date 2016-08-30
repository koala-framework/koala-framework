<?php
class Kwc_Menu_Expanded_EditableItems_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['child'] = array(
            'class' => 'Kwc_Menu_Expanded_EditableItems_Generator',
            'component' => 'Kwc_Basic_Image_Component'
        );
        $ret['childModel'] = 'Kwc_Menu_Expanded_EditableItems_Model';

        $ret['componentName'] = trlKwfStatic('Menu');
        $ret['componentIcon'] = 'layout';
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['assetsAdmin']['dep'][] = 'KwfProxyPanel';
        $ret['assetsAdmin']['dep'][] = 'ExtGroupingGrid';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/List/List.js';

        $ret['extConfig'] = 'Kwc_Menu_Expanded_EditableItems_ExtConfig';
        return $ret;
    }

    public function attachEditableToMenuData(&$menuData)
    {
        $children = array();
        foreach ($this->getData()->getChildComponents(array('generator'=>'child', 'ignoreVisible'=>true)) as $c) {
            $children[$c->row->target_page_id] = $c;
        }
        foreach ($menuData as $k=>$i) {
            $menuData[$k]['editable'] = $children[$i['data']->componentId];
        }
    }
}
