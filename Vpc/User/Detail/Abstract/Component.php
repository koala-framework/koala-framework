<?php
abstract class Vpc_User_Detail_Abstract_Component extends Vpc_Abstract_Composite_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->getData()->parent->row;
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $row = $this->getData()->parent->row;
        $ret[] = array(
            'model' => $row->getModel(),
            'id' => $row->id
        );
        return $ret;
    }
}
