<?php
class Kwc_Basic_LinkTag_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $data = $data->getChildComponent('-child');
        if (!$data) return '';
        return Kwc_Admin::getInstance($data->componentClass)->componentToString($data);
    }

    public function gridColumns()
    {
        $ret = parent::gridColumns();
        $ret['string']->setHeader(trlKwf('Link target'));
        return $ret;
    }

    public function getPagePropertiesForm($config)
    {
        $ret = new Kwc_Basic_LinkTag_Form(null, $this->_class);
        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Link'));
        foreach ($ret as $f) {
            $ret->fields->remove($f);
            $fs->add($f);
        }
        $ret->add($fs);
        return $ret;
    }
}
