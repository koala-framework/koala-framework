<?php
class Kwc_Composite_Downloads_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $downloadCount = count($data->getChildComponents());
        return $downloadCount.' Download'.($downloadCount == 1 ? '' : 's');
    }
}
