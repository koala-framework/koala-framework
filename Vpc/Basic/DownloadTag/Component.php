<?php
class Vpc_Basic_DownloadTag_Component extends Vpc_Abstract implements Vps_Media_Output_Interface
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'modelname'     => 'Vpc_Basic_DownloadTag_Model',
            'componentName' => 'Download Tag',
            'componentIcon' => new Vps_Asset('folder_link'),
            'default'   => array(
            )
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsSwfUpload';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/DownloadTag/Panel.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();

        $row = $this->_getRow();
        $filename = $row->filename != '' ? $row->filename : 'unnamed';


        $ret['filesize'] = $this->getFilesize();
        $ret['url'] = $this->getDownloadUrl();
        $ret['filename'] = $filename;
        return $ret;
    }

    public function hasContent()
    {
        $row = $this->getFileRow();
        if ($row && $row->vps_upload_id) {
            return true;
        }
        return false;
    }

    public function getDownloadUrl()
    {
        $row = $this->getFileRow();
        $fRow = $row->getParentRow('File');
        if (!$fRow) {
            return null;
        }
        $filename = $row->filename;
        if (!$filename) {
            $filename = $fRow->filename;
        }
        $filename .= '.'.$fRow->extension;
        $id = $this->getData()->componentId;
        return Vps_Media::getUrl(get_class($this), $id, 'default', $filename);
    }

    public function getFilesize()
    {
        $fRow = $this->getFileRow()->getParentRow('File');
        if (!$fRow) return null;
        return $fRow->getFileSize();
    }

    public function getFileRow()
    {
        return $this->_getRow();
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $row = Vpc_Abstract::createModel($className)->getRow($id);
        if ($row) {
            $fileRow = $row->getParentRow('File');
        } else {
            $fileRow = false;
        }
        if (!$fileRow) {
            return null;
        } else {
            $file = $fileRow->getFileSource();
            $mimeType = $fileRow->mime_type;
        }
        if (!$file || !file_exists($file)) {
            return null;
        }
        return array(
            'file' => $file,
            'mimeType' => $mimeType
        );
    }
}
