<?php
class Vpc_Abstract_List_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['componentIcon'] = new Vps_Asset('page');
        $ret['generators']['child']['class'] = 'Vpc_Abstract_List_Trl_Generator';
        $ret['childModel'] = 'Vpc_Abstract_List_Trl_Model';

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/Trl/FullSizeEditPanel.js';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoGrid';
        $ret['assetsAdmin']['dep'][] = 'VpsComponent';

        $ret['extConfig'] = 'Vpc_Abstract_List_Trl_ExtConfigList';

        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $children = $this->getData()->getChildComponents(array('generator' => 'child'));

        // wird zweimal gesetzt. siehe kommentar in nicht-trl component
        $ret['children'] = $children;
        $childrenById = array();
        foreach ($children as $c) {
            $childrenById[$c->id] = $c;
        }
        foreach (array_keys($ret['listItems']) as $k) {
            $id = $ret['listItems'][$k]['data']->id;
            if (isset($childrenById[$id])) {
                $ret['listItems'][$k]['data'] = $childrenById[$id];
            } else {
                unset($ret['listItems'][$k]);
            }
        }
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
