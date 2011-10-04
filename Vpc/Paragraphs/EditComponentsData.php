<?php
class Vpc_Paragraphs_EditComponentsData extends Vps_Data_Abstract
{
    private $_componentClass;
    private $_componentConfigs = array();

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    protected function _getComponentClassByRow($row)
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_componentClass, 'paragraphs');
        return $classes[$row->component];
    }

    public function load($row)
    {
        $gen = Vps_Component_Generator_Abstract::getInstance($this->_componentClass, 'paragraphs');

        $edit = Vps_Component_Abstract_ExtConfig_Abstract::getEditConfigs($this->_getComponentClassByRow($row), $gen, '{componentId}-{0}', '');
        $this->_componentConfigs = array_merge($this->_componentConfigs, $edit['componentConfigs']);
        $ret = $edit['contentEditComponents'];

        $components = Vps_Component_Data_Root::getInstance()->getComponentsByDbId($row->component_id.'-'.$row->id, array('ignoreVisible'=>true));
        if (isset($components[0])) {
            foreach (Vps_Controller_Action_Component_PagesController::getSharedComponents($components[0]) as $cls=>$cmp) {
                $cfg = Vpc_Admin::getInstance($cls)->getExtConfig(Vps_Component_Abstract_ExtConfig_Abstract::TYPE_SHARED);
                foreach ($cfg as $k=>$c) {
                    if (!isset($this->_componentConfigs[$cls.'-'.$k])) {
                        $this->_componentConfigs[$cls.'-'.$k] = $c;
                    }
                    $ret[] = array(
                        'componentClass' => $cls,
                        'type' => $k,
                        'idTemplate' => '{componentId}-{0}',
                        'componentIdSuffix' => ''
                    );
                }
            }
        }
        return $ret;
    }

    public function getComponentConfigs()
    {
        return $this->_componentConfigs;
    }
}
