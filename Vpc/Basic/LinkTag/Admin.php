<?php
class Vpc_Basic_LinkTag_Admin extends Vpc_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        $data = $data->getChildComponent('-link');
        if (!$data) return '';
        return Vpc_Admin::getInstance($data->componentClass)->componentToString($data);
    }

    public function gridColumns()
    {
        $ret = parent::gridColumns();
        $ret['string']->setHeader(trlVps('Link target'));
        return $ret;
    }
}
