<?php
class Vpc_Box_InheritContent_CacheMeta extends Vps_Component_Cache_Meta_Static_Component
{
    public static function getDeleteDbId($row, $dbId)
    {
        $pos = strrpos($dbId, '_');
        $pos = strpos($dbId, '-', $pos);
        $pageId = substr($dbId, 0, $pos);
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            $generators = Vpc_Abstract::getSetting($class, 'generators');
            if (isset($generators['page']) &&
                is_instance_of($generators['page']['class'], 'Vpc_Root_Category_Generator'))
            {
                $pagesModel = Vps_Component_Generator_Abstract::getInstance($class, 'page')->getModel();
            }
        }
        $ret = array();
        foreach (Vpc_Basic_ParentContent_CacheMeta::getRecursiveChildIds($pageId, $pagesModel) as $id) {
            $ret[] = $id . '%';
        }
        return $ret;
    }
}
