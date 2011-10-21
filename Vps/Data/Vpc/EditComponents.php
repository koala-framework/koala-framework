<?php
class Vps_Data_Vpc_EditComponents extends Vps_Data_Abstract
{
    private $_componentClass;
    private $_generatorKey;
    private $_componentConfigs = array();

    public function __construct($componentClass, $generatorKey = 'paragraphs')
    {
        $this->_componentClass = $componentClass;
        $this->_generatorKey = $generatorKey;
    }

    protected function _getComponentClassByRow($row)
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_componentClass, $this->_generatorKey);
        if (!$row->getModel()->hasColumn('component') || !$row->component) {
            return reset($classes);
        }
        return $classes[$row->component];
    }

    public function load($row)
    {
        $gen = Vps_Component_Generator_Abstract::getInstance($this->_componentClass, $this->_generatorKey);

        $edit = Vps_Component_Abstract_ExtConfig_Abstract::getEditConfigs($this->_getComponentClassByRow($row), $gen, '{componentId}-{0}', '');
        $this->_componentConfigs = array_merge($this->_componentConfigs, $edit['componentConfigs']);
        $ret = $edit['contentEditComponents'];

        $components = Vps_Component_Data_Root::getInstance()->getComponentsByDbId(
            $row->component_id.$gen->getIdSeparator().$row->id, array('ignoreVisible'=>true)
        );
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
