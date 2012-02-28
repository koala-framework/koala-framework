<?php
class Kwc_Basic_Headlines_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        return $data->getComponent()->getRow()->headline1;
    }
}
