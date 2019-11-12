<?php
class Kwc_Composite_SwitchDisplay_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        return array(
            'startOpened' => (boolean)$row->start_opened,
            'linktext' => $data->getChildComponent('-linktext'),
            'content' => $data->getChildComponent('-content')
        );
    }
}
