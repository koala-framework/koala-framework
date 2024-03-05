<?php
class Kwc_Abstract_List_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        return array(
            'children' => array_values($data->getChildComponents(array('generator'=>'child')))
        );
    }
}
