<?php
class Kwc_Legacy_Headline_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        return $data->getComponent()->getRow()->headline1;
    }
}
