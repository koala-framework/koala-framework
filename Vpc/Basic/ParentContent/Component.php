<?php
/**
 * @package Vpc
 * @subpackage Basic
 */
class Vpc_Basic_ParentContent_Component extends Vpc_Abstract
{
    public function getTemplateVars()
    {
        $ret = array();
        $data = $this->getData();
        $ids = array();
        while ($data && !$data->inherits) {
            $ids[] = strrchr($data->componentId, '-');
            $data = $data->parent;
        }
        while ($data) {
            if ($data->inherits) {
                $d = $data;
                foreach (array_reverse($ids) as $id) {
                    $d = $d->getChildComponent($id);
                }
                if ($d->componentClass != $this->getData()->componentClass) {
                    $ret['parentComponent'] = $d;
                }
            }
            $data = $data->parent;
        }
        return $ret;
    }

    public function hasContent()
    {
        //TODO, ist mit cache loeschen womoeglich ein problem
        return true;
    }
}
