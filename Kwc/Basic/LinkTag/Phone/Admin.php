<?php
class Kwc_Basic_LinkTag_Phone_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
{
    // wird bei linklist verwendet, damit url richtig ausgegeben wird
    public function componentToString(Kwf_Component_Data $data)
    {
        $ret = parent::componentToString($data);
        $punycode = new Kwf_Util_Punycode();
        return $punycode->decode($ret);
    }
}
