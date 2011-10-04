<?php
class Vpc_Root_CategoryGenerator extends Vps_Component_Generator_Table
{
    protected $_hasNumericIds = false;
    protected $_inherits = true;

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'folder';
        return $ret;
    }

    protected function _formatConfig($parentData, $id)
    {
        $ret = parent::_formatConfig($parentData, $id);
        $ret['name'] = $ret['row']->name;
        return $ret;
    }

    protected function _getParentDataByRow($row, $select = null)
    {
        if (is_instance_of($this->_class, 'Vpc_Root_Component')) {
            return Vps_Component_Data_Root::getInstance();
        }
        if ($select->hasPart(Vps_Component_Select::WHERE_SUBROOT)) {
            $subroot = $select->getPart(Vps_Component_Select::WHERE_SUBROOT);
            $component = $subroot[0];
            while ($component->parent->componentId != 'root') $component = $component->parent;
            if ($component->componentClass == $this->getClass()) {
                return $component;
            }
            return null;
        }
        return Vps_Component_Data_Root::getInstance()->getComponentsBySameClass($this->_class);
    }

    public function getChildData($parentData, $select = array())
    {
        if (is_array($select)) $select = new Vps_Component_Select($select);
        if (!is_instance_of($this->_class, 'Vpc_Root_Component') && $select->hasPart(Vps_Component_Select::WHERE_SUBROOT)) {
            //abkürzung wenn mehrere domains mit unterschiedlichen component-Klassen
            //im prinzip gleicher code wie in _GetParentDataByRow wenn return null gemacht wird, aber das hier wird früher gemacht
            $subroot = $select->getPart(Vps_Component_Select::WHERE_SUBROOT);
            $component = $subroot[0];
            while ($component->parent->componentId != 'root') $component = $component->parent;
            if ($component->componentClass != $this->getClass()) {
                Vps_Benchmark::count('GenTable::getChildData skipped');
                return array();
            }
        }
        return parent::getChildData($parentData, $select);
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['showInPageTreeAdmin'] = true;
        return $ret;
    }
}
