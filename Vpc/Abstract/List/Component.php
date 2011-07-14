<?php
abstract class Vpc_Abstract_List_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => 'List',
            'childModel'     => 'Vpc_Abstract_List_Model',
            'ownModel'     => 'Vpc_Abstract_List_OwnModel',
        ));
        $ret['generators']['child'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'component' => null
        );
        $ret['assetsAdmin']['dep'][] = 'VpsProxyPanel';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoGrid';
        $ret['assetsAdmin']['dep'][] = 'VpsMultiFileUploadPanel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/EditButton.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/PanelWithEditButton.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/List.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/ListEditButton.js';
        $ret['extConfig'] = 'Vpc_Abstract_List_ExtConfigListUpload';
        $ret['hasVisible'] = true;
        return $ret;
    }

    public static function validateSettings($settings)
    {
        if (isset($settings['default'])) {
            throw new Vps_Exception("Setting default doesn't exist anymore");
        }
    }

    //kann überschrieben werden um zB ein limit einzubauen
    protected function _getSelect()
    {
        $select = new Vps_Component_Select();
        $select->whereGenerator('child');
        return $select;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $children = $this->getData()->getChildComponents($this->_getSelect());

        // children ist die alte methode, bleibt drin wegen kompatibilität
        $ret['children'] = $children;

        // das hier ist die neue variante und ist besser, weil man leichter mehr daten
        // zurückgeben kann, bzw. in der übersetzung überschreiben kann
        // zB: Breite bei übersetzung von Columns
        $ret['listItems'] = array();
        $i = 0;
        foreach ($children as $child) {
            $class = 'listItem ';
            if ($i == 0) $class .= 'vpcFirst ';
            if ($i == count($children)-1) $class .= 'vpcLast ';
            if ($i % 2 == 0) {
                $class .= 'vpcEven ';
            } else {
                $class .= 'vpcOdd ';
            }
            $class = trim($class);
            $i++;

            $ret['listItems'][] = array(
                'data' => $this->_getItemComponent($child),
                'class' => $class
            );
        }
        return $ret;
    }

    protected function _getItemComponent($childComponent)
    {
        return $childComponent;
    }

    public function getExportData()
    {
        $ret = array('list' => array());
        $children = $this->getData()->getChildComponents($this->_getSelect());
        foreach ($children as $child) {
            $ret['list'][] = $child->getComponent()->getExportData();
        }
        return $ret;
    }

    public function hasContent()
    {
        $childComponents = $this->getData()->getChildComponents($this->_getSelect());
        foreach ($childComponents as $c) {
            if ($c->hasContent()) return true;
        }
        return false;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Vps_Component_Cache_Meta_Static_ChildModel();
        return $ret;
    }
}
