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
            'modelname'     => 'Vpc_Basic_LinkTag_Intern_Model',
            'componentName' => trlVps('Link.Intern'),
        ));
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/LinkTag/Intern/LinkField.js';
        return $ret;
    }
    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $parent = $this->getData()->parent;
        if ($parent->getComponent() instanceof Vpc_Basic_LinkTag_Component) {
            //der typ vom link-tag kann sich ändern, und hat die gleiche cache-id
            //darum löschen
            $model = $parent->getComponent()->getModel();
            $row = $model->getRow($parent->dbId);
            if ($row) {
                $ret[] = array(
                    'model' => $model,
                    'id' => $row->component_id
                );
                $ret[] = array(
                    'model' => $model,
                    'id' => $row->component_id,
                    'callback' => true
                );
            }
        }
        //zB eine news von der der status geändert wird oder der titel geändert wird
        $ret = array_merge($ret, $this->getData()->getLinkedData()
                                            ->getComponent()->getCacheVars());
        return $ret;
    }
}
