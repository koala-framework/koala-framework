<?php
class Kwc_Basic_LinkTag_Extern_Trl_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        return $data->url;
    }
}
