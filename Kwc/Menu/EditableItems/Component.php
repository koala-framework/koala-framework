<?php
class Vpc_Menu_EditableItems_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['child'] = array(
            'class' => 'Vpc_Menu_EditableItems_Generator',
            'component' => 'Vpc_Basic_Image_Component'
        );
        $ret['childModel'] = 'Vpc_Menu_EditableItems_Model';

        $ret['componentName'] = trlVps('Menu');
        $ret['componentIcon'] = new Vps_Asset('layout');
        $ret['cssClass'] = 'webStandard';
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/List.js';

        $ret['extConfig'] = 'Vpc_Menu_EditableItems_ExtConfig';
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
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vps_Component_Model');
        $ret[] = new Vps_Component_Cache_Meta_Static_Model('Vpc_Root_Category_GeneratorModel');
        return $ret;
    }
}
