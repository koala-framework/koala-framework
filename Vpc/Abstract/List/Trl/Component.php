<?php
class Vpc_Abstract_List_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['componentIcon'] = new Vps_Asset('page');
        $ret['generators']['child']['class'] = 'Vpc_Abstract_List_Trl_Generator';
        $ret['childModel'] = 'Vpc_Abstract_List_Trl_Model';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['children'] = $this->getData()
            ->getChildComponents(array('generator'=>'child'));
        return $ret;
    }

    public function getExportData()
    {
        $ret = array('list' => array());
        $children = $this->getData()->getChildComponents(array('generator' => 'child'));
        foreach ($children as $child) {
            $ret['list'][] = $child->getComponent()->getExportData();
        }
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        foreach ($this->getData()->getChildComponents(array('generator'=>'child', 'ignoreVisible'=>true)) as $p) {
            $ret[] = array(
                'model' => $this->getChildModel(),
                'id' => $p->dbId,
                'field' => 'component_id'
            );
        }
        return $ret;
    }
}
