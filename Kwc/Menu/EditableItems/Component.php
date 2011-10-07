<?php
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

        $ret['componentName'] = trlKwf('Menu');
        $ret['componentIcon'] = new Kwf_Asset('layout');
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
            $menuData[$k]['editable'] = $children[$i['data']->componentId];
        }
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        // Ist ziemlich grob, sonst müsste man sich eigenes Meta schreiben
        // Wenn eine Unterseite zB. offline genommen wird, muss der Cache gelöscht werden
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwf_Component_Model');
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwc_Root_Category_GeneratorModel');
        return $ret;
    }
}
