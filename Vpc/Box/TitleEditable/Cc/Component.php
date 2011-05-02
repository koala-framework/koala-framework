<?php
class Vpc_Box_TitleEditable_Cc_Component extends Vpc_Chained_Cc_Component
{
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $data = $this->getData();
        do {
            $data = $data->parent;
        } while ($data && !is_instance_of($data->componentClass, 'Vpc_Ingenieurbueros_Root_Cc_Chained_Component'));

        if ($data) {
            $ret['title'] .= ' '.$data->name;
        }
        return $ret;
    }
}
