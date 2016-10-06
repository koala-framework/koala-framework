<?php
class Kwc_Basic_LinkTag_ComponentClass_Data extends Kwc_Basic_LinkTag_Intern_Data
{
    protected function _getData($select = array())
    {
        $m = Kwc_Abstract::createOwnModel($this->componentClass);
        if ($column = $m->fetchColumnByPrimaryId('target_component_id', $this->dbId)) {
            return Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($column, array('subroot' => $this));
        }
        return false;
    }
}
