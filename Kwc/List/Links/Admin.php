<?php
class Kwc_List_Links_Admin extends Kwc_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $linkCount = count($data->getChildComponents());
        return $linkCount.' Link'.($linkCount == 1 ? '' : 's');
    }
}
