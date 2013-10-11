<?php
class Kwc_Basic_LinkTag_Trl_Admin extends Kwc_Basic_LinkTag_Admin
{
    public function getPagePropertiesForm()
    {
        $ret = new Kwc_Abstract_Cards_Trl_Form(null, $this->_class);
        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Link'));
        foreach ($ret as $f) {
            $ret->fields->remove($f);
            $fs->add($f);
        }
        $ret->add($fs);
        return $ret;
    }
}
