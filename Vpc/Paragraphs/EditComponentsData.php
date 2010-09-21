<?php
class Vpc_Paragraphs_EditComponentsData extends Vps_Data_Abstract
{
    private $_componentClass;
    private $_componentConfigs = array();

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    //teilw. übernommen von Vpc_Directories_Item_Directory_ExtConfigAbstract
    //TODO: code sharen
    private function _getEditConfigs($componentClass, Vps_Component_Generator_Abstract $gen, $idTemplate, $componentIdSuffix)
    {
        $ret = array();
        $cfg = Vpc_Admin::getInstance($componentClass)->getExtConfig();
        foreach ($cfg as $k=>$c) {
            if (!isset($this->_componentConfigs[$componentClass.'-'.$k])) {
                $this->_componentConfigs[$componentClass.'-'.$k] = $c;
            }
            $ret[] = array(
                'componentClass' => $componentClass,
                'type' => $k,
                'idTemplate' => $idTemplate,
                'componentIdSuffix' => $componentIdSuffix
            );
        }
        foreach ($gen->getGeneratorPlugins() as $plugin) {
            $cls = get_class($plugin);
            $cfg = Vpc_Admin::getInstance($cls)->getExtConfig();
            foreach ($cfg as $k=>$c) {
                if (!isset($this->_componentConfigs[$cls.'-'.$k])) {
                    $this->_componentConfigs[$cls.'-'.$k] = $c;
                }
                $ret[] = array(
                    'componentClass' => $cls,
                    'type' => $k,
                    'idTemplate' => $idTemplate,
                    'componentIdSuffix' => $componentIdSuffix
                );
            }
        }
        if (Vpc_Abstract::hasSetting($componentClass, 'editComponents')) {
            $editComponents = Vpc_Abstract::getSetting($componentClass, 'editComponents');
            foreach ($editComponents as $c) {
                $childGen = Vps_Component_Generator_Abstract::getInstances($componentClass, array('componentKey'=>$c));
                $childGen = $childGen[0];
                $cls = Vpc_Abstract::getChildComponentClass($componentClass, null, $c);
                $edit = $this->_getEditConfigs($cls, $childGen,
                                               $idTemplate,
                                               $componentIdSuffix.$childGen->getIdSeparator().$c);
                $ret = array_merge($ret, $edit);
            }
        }
        return $ret;
    }

    protected function _getComponentClassByRow($row)
    {
        $classes = Vpc_Abstract::getChildComponentClasses($this->_componentClass, 'paragraphs');
        return $classes[$row->component];
    }

    public function load($row)
    {
        $gen = Vps_Component_Generator_Abstract::getInstance($this->_componentClass, 'paragraphs');
        $ret = $this->_getEditConfigs($this->_getComponentClassByRow($row), $gen, '{componentId}-{0}', '');
        $component = Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id.'-'.$row->id, array('ignoreVisible'=>true));
        foreach (Vps_Controller_Action_Component_PagesController::getSharedComponents($component) as $cls=>$cmp) {
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
        return $ret;
    }

    public function getComponentConfigs()
    {
        return $this->_componentConfigs;
    }
}
