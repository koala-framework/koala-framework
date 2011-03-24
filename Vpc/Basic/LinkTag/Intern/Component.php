<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_LinkTag_Intern_Component extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'dataClass' => 'Vpc_Basic_LinkTag_Intern_Data',
            'ownModel'     => 'Vpc_Basic_LinkTag_Intern_Model',
            'componentName' => trlVps('Link.Intern'),
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsPageSelectField';
        return $ret;
    }
    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $linkedData = $this->getData()->getLinkedData();
        if ($linkedData && isset($linkedData->row) && $linkedData->row) {
            // wenns kein chained gibt, hat man in der variable $chainedData
            // das normale $linkedData drin.
            $chainedData = $linkedData;
            while ($chainedData->row instanceof Vpc_Root_Category_Trl_GeneratorRow) {
                $chainedData = $chainedData->chained;
            }
            if ($linkedData->row instanceof Vpc_Root_Category_Trl_GeneratorRow) {
                $ret[] = array(
                    'model' => 'Vpc_Root_Category_Trl_GeneratorModel',
                    'id' => $linkedData->row->component_id
                );
            } 
            $ret[] = array(
                'model' => 'Vps_Component_Model',
                'id' => $chainedData->row->id
            );
            $ret[] = array(
                'model' => 'Vpc_Root_Category_GeneratorModel',
                'id' => $chainedData->row->id
            );
            if ($linkedData instanceof Vpc_Basic_LinkTag_FirstChildPage_Data) {
                $childData = $linkedData->_getFirstChildPage();
                $ret = array_merge($ret, $childData->getComponent()->getCacheVars());
            }
        }
        return $ret;
    }
}
