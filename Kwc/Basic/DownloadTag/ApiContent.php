<?php
class Kwc_Basic_DownloadTag_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        /** @var Kwc_Basic_DownloadTag_Component $component */
        $component = $data->getComponent();
        $fileRow = $component->getFileRow();
        $uploadRow = $fileRow->getParentRow('File');

        $ret = array();
        $ret['downloadUrl'] = $component->getDownloadUrl();
        $ret['rel'] = $data->rel;
        $ret['open_type'] = $fileRow->open_type;
        $ret['content_disposition'] = $fileRow->content_disposition;
        if ($fileRow->filename != '') {
            $ret['filename'] = $fileRow->filename;
        }
        if ($uploadRow) {
            $ret['size'] = $component->getFilesize();
            $ret['mime_type'] = $uploadRow->mime_type;
            $ret['extension'] = $uploadRow->extension;
        }
        return $ret;
    }
}
