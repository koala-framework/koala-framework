<?php
class Kwc_TextImage_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $ret = array();
        $row = $data->getComponent()->getRow();
        if ($row->image) {
            if ($row->position) {
                $ret['position'] = $row->position;
            }
            if ($row->image_width) {
                $ret['imageWidth'] = $row->image_width;
            }
            if ($row->flow) {
                $ret['flow'] = !!$row->flow;
            }
            $ret['image'] = $data->getChildComponent('-image');
        }
        $ret['text'] = $data->getChildComponent('-text');
        return $ret;
    }
}
