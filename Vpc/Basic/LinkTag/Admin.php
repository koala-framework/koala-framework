<?php
class Vpc_Basic_LinkTag_Admin extends Vpc_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        $data = $data->getChildComponent('-child');
        if (!$data) return '';
        return Vpc_Admin::getInstance($data->componentClass)->componentToString($data);
    }

    public function gridColumns()
    {
        $ret = parent::gridColumns();
        $ret['string']->setHeader(trlVps('Link target'));
        return $ret;
    }

    public function getPagePropertiesForm()
    {
        $ret = new Vpc_Basic_LinkTag_Form(null, $this->_class);
        $fs = new Vps_Form_Container_FieldSet(trlVps('Link'));
        foreach ($ret as $f) {
            $ret->fields->remove($f);
            $fs->add($f);
        }
        $ret->add($fs);
        return $ret;
    }
}
