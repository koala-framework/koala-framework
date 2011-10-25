<?php
class Kwc_Box_InheritContent_CacheMeta extends Kwf_Component_Cache_Meta_Static_Component
{
    public static function getDeleteDbId($row, $dbId)
    {
        $pos = strrpos($dbId, '_');
        $pos = strpos($dbId, '-', $pos);
        $pageId = substr($dbId, 0, $pos);
        foreach (Kwc_Abstract::getComponentClasses() as $class) {
            $generators = Kwc_Abstract::getSetting($class, 'generators');
            if (isset($generators['page']) &&
                is_instance_of($generators['page']['class'], 'Kwc_Root_Category_Generator'))
            {
                $pagesModel = Kwf_Component_Generator_Abstract::getInstance($class, 'page')->getModel();
            }
        }
        $ret = array();
        foreach (Kwc_Basic_ParentContent_CacheMeta::getRecursiveChildIds($pageId, $pagesModel) as $id) {
            $ret[] = $id . '%';
        }
        return $ret;
    }
}
