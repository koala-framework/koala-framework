<?php
class Vpc_Basic_LinkTag_Mail_Trl_Admin extends Vpc_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        return $data->url_mail_txt;
    }
}
