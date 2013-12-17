<?php
class Kwc_Legacy_Headlines_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        return $data->getComponent()->getRow()->headline1;
    }
}
