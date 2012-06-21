<?php
class Kwc_Basic_DownloadTag_Component extends Kwc_Basic_LinkTag_Abstract_Component
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'ownModel'     => 'Kwc_Basic_DownloadTag_Model',
            'componentName' => trlKwfStatic('Download'),
            'componentIcon' => new Kwf_Asset('folder_link'),
        ));
        $ret['dataClass'] = 'Kwc_Basic_DownloadTag_Data';
        $ret['assetsAdmin']['dep'][] = 'KwfFormFile';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/DownloadTag/Panel.js';
        $ret['throwHasContentChangedOnRowColumnsUpdate'] = 'kwf_upload_id';
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
        $retValid = self::VALID;
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id);
        if (!$c) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));
            if (!$c) return self::INVALID;
            if (Kwf_Component_Data_Root::getShowInvisible()) {
                //preview im frontend
                $retValid = self::VALID_DONT_CACHE;
            } else if (Kwf_Registry::get('acl')->isAllowedComponentById($id, $className, Kwf_Registry::get('userModel')->getAuthedUser())) {
                //paragraphs vorschau im backend
                $retValid = self::VALID_DONT_CACHE;
            } else {
                return self::ACCESS_DENIED;
            }
        }
        while ($c) {
            foreach (Kwc_Abstract::getSetting($c->componentClass, 'plugins') as $plugin) {
                if (is_instance_of($plugin, 'Kwf_Component_Plugin_Interface_Login')) {
                    $plugin = new $plugin($id);
                    if ($plugin->isLoggedIn()) {
                        return self::VALID_DONT_CACHE;
                    } else {
                        return self::ACCESS_DENIED;
                    }
                }
            }
            if ($c->isPage) break;
            $c = $c->parent;
        }
        return $retValid;
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
        return array(
            'file' => $file,
            'mimeType' => $mimeType
        );
    }
}