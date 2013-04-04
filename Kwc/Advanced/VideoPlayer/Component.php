<?php
class Kwc_Advanced_VideoPlayer_Component extends Kwc_Abstract_Composite_Component
    implements Kwf_Media_Output_IsValidInterface
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Videoplayer';
        $ret = array_merge(parent::getSettings(), array(
            'ownModel'     => 'Kwc_Advanced_VideoPlayer_Model',
            'componentName' => trlKwfStatic('Video'),
            'componentIcon' => new Kwf_Asset('images'),
            'extConfig' => 'Kwf_Component_Abstract_ExtConfig_Form'
        ));
//         $ret['dataClass'] = 'Kwc_Basic_DownloadTag_Data';
        $ret['assets']['files'][] = 'kwf/Kwc/Advanced/VideoPlayer/Component.js';
        $ret['assetsAdmin']['dep'][] = 'KwfFormFile';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['webmSource'] = Kwf_Media::getUrl($this->getData()->componentClass,
            $this->getData()->componentId, 'webm', 'test.webm');
        $ret['mp4Source'] = Kwf_Media::getUrl($this->getData()->componentClass,
            $this->getData()->componentId, 'mp4', 'test.mp4');
        return $ret;
    }

    public static function isValidMediaOutput($id, $type, $className)
    {
        return Kwf_Media_Output_IsValidInterface::VALID;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        $component =Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id);
        $row = $component->getComponent()->getRow();
        if ($type == 'webm') {
            $uploadRow = $row->getParentRow('FileWebm');
        } else if ($type == 'mp4') {
            $uploadRow = $row->getParentRow('FileMp4');
        }
        if (!$uploadRow) return null;
        return array(
            'file'=>$uploadRow->getFileSource(),
            'mimeType' => $uploadRow->mime_type
        );
    }
}
