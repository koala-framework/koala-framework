<?php
class Vpc_Basic_LinkTag_Extern_Admin extends Vpc_Basic_LinkTag_Abstract_Admin
{
    // wird bei linklist verwendet, damit url richtig ausgegeben wird
    public function componentToString($data)
    {
        $ret = parent::componentToString($data);
        $punycode = new Vps_Util_Punycode();
        return $punycode->decode($ret);
    }
}
