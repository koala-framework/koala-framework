<?php
class Vpc_Root_DomainRoot_Domain_CategoryGenerator extends Vpc_Root_CategoryGenerator
{
    protected function _getParentDataByRow($row, $select = null)
    {
        if ($select->hasPart(Vps_Component_Select::WHERE_SUBROOT)) {
            $subroot = $select->getPart(Vps_Component_Select::WHERE_SUBROOT);
            $component = $subroot[0];
            while ($component->parent->componentId != 'root') $component = $component->parent;
            if ($component->componentClass == $this->getClass()) {
                return $component;
            }
            return null;
        }
        throw new Vps_Exception('Not subroot given, cannot find corrent domain.');
    }
}
