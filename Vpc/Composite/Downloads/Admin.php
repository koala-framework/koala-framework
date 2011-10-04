<?php
class Vpc_Composite_Downloads_Admin extends Vpc_Admin
{
    public function componentToString(Vps_Component_Data $data)
    {
        $downloadCount = count($data->getChildComponents());
        return $downloadCount.' Download'.($downloadCount == 1 ? '' : 's');
    }
}
