<?php
class Kwc_Basic_LinkTag_Empty_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        return '('.trlKwf('none').')';
    }
}
