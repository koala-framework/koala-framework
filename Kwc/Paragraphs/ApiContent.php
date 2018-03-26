<?php
class Kwc_Paragraphs_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $ret = array();
        $ret['paragraphs'] = array();
        foreach($data->getChildComponents(array('generator'=>'paragraphs')) as $paragraph) {
            $ret['paragraphs'][] = $paragraph;
        }
        return $ret;
    }
}
