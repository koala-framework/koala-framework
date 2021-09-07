<?php
class Kwc_Basic_DownloadTag_Component extends Kwc_Basic_LinkTag_Abstract_Component
    implements Kwf_Media_Output_IsValidInterface, Kwf_Media_Output_ClearCacheInterface
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
            'ownModel'     => 'Kwc_Basic_DownloadTag_Model',
            'componentName' => trlKwfStatic('Download'),
            'componentIcon' => 'folder_link',
            'hasPopup'      => true,
            'openType'      => null, //wenn hasPopup auf false
        ));
        $ret['dataClass'] = 'Kwc_Basic_DownloadTag_Data';
        $ret['assetsAdmin']['dep'][] = 'KwfFormFile';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/DownloadTag/Panel.js';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'kwf_upload_id';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

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
        if ($row && $row->kwf_upload_id) {
            return true;
        }
        return false;
    }

    public function getDownloadUrl()
    {
        return $this->getData()->url;
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

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_Component::isValid($id);
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
        if (!$c) return null;
        $row = $c->getComponent()->getRow();
        $fileRow = $row->getParentRow('File');
        if (!$fileRow) {
            return null;
        } else {
            $file = $fileRow->getFileSource();
            $mimeType = $fileRow->mime_type;
        }
        if (!$file || !file_exists($file)) {
            return null;
        }

        $filename = $row->filename != '' ? $row->filename : 'unnamed';
        $filename .= '.'.$fileRow->extension;

        $ret = array(
            'file' => $file,
            'mimeType' => $mimeType,
        );
        if ($row->content_disposition === 'attachment') {
            $ret['downloadFilename'] = $filename;
        } else if ($row->content_disposition === 'inline') {
            $ret['filename'] = $filename;
        }

        if ($row->rel_noindex) header("X-Robots-Tag: \"noindex\"");

        return $ret;
    }

    public static function canCacheBeDeleted($id)
    {
        return !Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
    }
}
