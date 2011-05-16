<?php
class Vpc_Composite_Links_Admin extends Vpc_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        $linkCount = count($data->getChildComponents());
        return $linkCount.' Link'.($linkCount == 1 ? '' : 's');
    }
}
