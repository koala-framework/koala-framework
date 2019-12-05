<?php
class Kwc_Advanced_CommunityVideo_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $ret = array();
        $row = $data->getComponent()->getRow();
        $ret['videoUrl'] = $row->url;
        if ($row->size == 'custom') {
            $ret['videoWidth'] = $row->width;
            $ret['videoHeight'] = $row->height;
        }
        $ret['autoPlay'] = !!$row->autoplay;
        $ret['loop'] = !!$row->loop;
        $ret['format'] = $row->ratio;
        $ret['showSimilarVideos'] = !!$row->show_similar_videos;
        return $ret;
    }
}
