<?php
class Kwc_Advanced_Youtube_Admin extends Kwc_Abstract_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        return $data->getComponent()->getRow()->url;
    }
}
