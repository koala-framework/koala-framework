<?php
class Vpc_Basic_LinkTag_Intern_Cc_Component extends Vpc_Basic_LinkTag_Abstract_Cc_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['dataClass'] = 'Vpc_Basic_LinkTag_Intern_Cc_Data';
        return $ret;
    }

    // TODO: Cache
    /*
    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $linkedData = $this->getData()->getLinkedData();
        if ($linkedData && isset($linkedData->row) && $linkedData->row) {
            if ($linkedData->row instanceof Vpc_Root_Category_Trl_GeneratorRow) {
                $ret[] = array(
                    'model' => 'Vps_Component_Model',
                    'id' => $linkedData->chained->row->id
                );
                $ret[] = array(
                    'model' => 'Vpc_Root_Category_GeneratorModel',
                    'id' => $linkedData->chained->row->id
                );
                $ret[] = array(
                    'model' => 'Vpc_Root_Category_Trl_GeneratorModel',
                    'id' => $linkedData->row->component_id
                );
            } else {
                $ret[] = array(
                    'model' => 'Vps_Component_Model',
                    'id' => $linkedData->row->id
                );
                $ret[] = array(
                    'model' => 'Vpc_Root_Category_GeneratorModel',
                    'id' => $linkedData->row->id
                );
            }
            if ($linkedData instanceof Vpc_Basic_LinkTag_FirstChildPage_Data) {
                $childData = $linkedData->_getFirstChildPage();
                $ret = array_merge($ret, $childData->getComponent()->getCacheVars());
            }
        }
        return $ret;
    }
    */
}
