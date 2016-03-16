<?php
/**
 * This component can be added as child to a Kwc_Menu_Abstract_Component and provides
 * an instance of 'child' component per menu item.
 *
 * Usually the menu template has to be overwritten and the associated child for the menu item
 * is found in 'editable' index.
 *
 * Example (from Menu Component.ptl):
 *     <?=$this->componentLink($m['data'], $this->component($m['editable']->getChildComponent('-image')).$m['text']);?>
 *
 */
class Kwc_Menu_EditableItems_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['child'] = array(
            'class' => 'Kwc_Menu_EditableItems_Generator',
            'component' => 'Kwc_Basic_Image_Component'
        );
        $ret['childModel'] = 'Kwc_Menu_EditableItems_Model';

        $ret['componentName'] = trlKwfStatic('Menu');
        $ret['componentIcon'] = 'layout';
        $ret['cssClass'] = 'webStandard';
        $ret['assetsAdmin']['dep'][] = 'KwfProxyPanel';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Abstract/List/List.js';

        $ret['extConfig'] = 'Kwc_Menu_EditableItems_ExtConfig';
        return $ret;
    }

    public function attachEditableToMenuData(&$menuData)
    {
        $children = array();
        foreach ($this->getData()->getChildComponents(array('generator'=>'child')) as $c) {
            $children[$c->row->target_page_id] = $c;
        }
        foreach ($menuData as $k=>$i) {
            $menuData[$k]['editable'] = isset($children[$i['data']->componentId]) ? $children[$i['data']->componentId] : null;
        }
    }
}
