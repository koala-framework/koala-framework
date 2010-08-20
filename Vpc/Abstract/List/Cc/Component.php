<?php
class Vpc_Abstract_List_Cc_Component extends Vpc_Chained_Cc_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $children = $this->getData()->getChildComponents(array('generator' => 'child'));

        // wird zweimal gesetzt. siehe kommentar in nicht-trl component
        $ret['children'] = $children;
        $ret['listItems'] = array();
        foreach ($children as $child) {
            $ret['listItems'][] = array(
                'data' => $child
            );
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
}
