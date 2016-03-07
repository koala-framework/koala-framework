<?php
class Kwc_Directories_Category_ShowCategories_Component extends Kwc_Directories_Category_ShowCategories_Abstract_Component
{
    private $_categories;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['showDirectoryClass'] = 'Kwc_Directories_Item_Directory_Component'; // nur für form
        $ret['hideDirectoryClasses'] = array();
        return $ret;
    }

    private function _getCategories()
    {
        if (!isset($this->_categories)) {
            $m = Kwf_Model_Abstract::getInstance('Kwc_Directories_Category_ShowCategories_Model');
            $this->_categories = $m->getRows($m->select()->whereEquals('component_id', $this->getDbId()));
        }
        return $this->_categories;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        $ret = array();
        $class = self::getSetting($directoryClass, 'showDirectoryClass');
        foreach (Kwc_Abstract::getComponentClasses() as $cls) {
            if (is_instance_of($cls, $class)) {
                foreach (self::getSetting($directoryClass, 'hideDirectoryClasses') as $hideClass) {
                    if (is_instance_of($cls, $hideClass)) {
                        continue 2;
                    }
                }
                $ret[] = $cls;
            }
        }
        return $ret;
    }

    protected function _getItemDirectory()
    {
        $categories = $this->_getCategories();
        if (count($categories)) {
            $componentId = $categories->current()->getParentRow('Category')->component_id;
            $component = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($componentId);
            if ($component) {
                return $component->parent;
            }
        }
        return null;
    }

    public function getCategoryIds()
    {
        $ids = array();
        foreach ($this->_getCategories() as $category) {
            $ids[] = Kwf_Registry::get('db')->quote($category->category_id);
        }
        return $ids;
    }
}
