<?php
class Kwc_Directories_TopChoose_Component extends Kwc_Directories_Top_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['showDirectoryClass'] = 'Kwc_Directories_Item_Directory_Component'; // nur für form
        $ret['ownModel'] = 'Kwc_Directories_TopChoose_Model';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';
        return $ret;
    }

    public static function getItemDirectoryClasses($directoryClass)
    {
        $ret = array();
        $class = self::getSetting($directoryClass, 'showDirectoryClass');
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByClass($class) as $component) {
            $ret[] = $component->componentClass;
        }
        return $ret;
    }

    protected function _getItemDirectory()
    {
        $row = $this->_getRow();
        if ($row && $row->directory_component_id) {
            $component = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($row->directory_component_id);
            if (is_instance_of($component->componentClass, 'Kwc_Directories_Item_DirectoryNoAdmin_Component')) {
                return $component;
            }
        }
        return null;
    }
}
