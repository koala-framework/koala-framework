<?php
class Kwc_Advanced_VideoPlayer_Admin extends Kwc_Abstract_Composite_Admin
{
    public function componentToString(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        if ($row->source_type == 'files') {
            if ($row->getParentRow('FileMp4')) {
                return $row->getParentRow('FileMp4')->filename.'.'.$row->getParentRow('FileMp4')->extension;
            } else if ($row->getParentRow('FileOgg')) {
                return $row->getParentRow('FileOgg')->filename.'.'.$row->getParentRow('FileOgg')->extension;
            } else if ($row->getParentRow('FileWebm')) {
                return $row->getParentRow('FileWebm')->filename.'.'.$row->getParentRow('FileWebm')->extension;
            }
        } else {
            if ($row->mp4_url) return $row->mp4_url;
            if ($row->ogg_url) return $row->ogg_url;
            if ($row->webm_url) return $row->webm_url;
        }
        return '';
    }
}
