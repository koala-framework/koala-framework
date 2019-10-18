<?php
class Kwc_TextImage_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $ret = array();
        $row = $data->getComponent()->getRow();
        if ($row->image) {
            $ret['position'] = $row->position;
            $ret['image'] = $data->getChildComponent('-image');
            $ret['imageWidth'] = $row->image_width;
            $ret['flow'] = $row->flow;
        }
        $ret['text'] = $data->getChildComponent('-text');
        return $ret;
    }
}
