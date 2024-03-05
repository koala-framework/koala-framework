<?php
class Kwc_List_Gallery_ApiContent extends Kwc_Abstract_List_ApiContent
{
    public function getContent(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $ret = parent::getContent($data);
        $ret['columns'] = $row->columns;
        if (Kwc_Abstract::getSetting($data->componentClass, 'showMoreLink')) {
            $ret['showPics'] = $row->show_pics;
            $ret['showMoreLinkText'] = $row->show_more_link_text;
        }
        return $ret;
    }
}
