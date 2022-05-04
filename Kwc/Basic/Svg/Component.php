<?php
class Kwc_Basic_Svg_Component extends Kwc_Abstract
    implements Kwf_Media_Output_Interface
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlStatic('SVG');
        $ret['ownModel'] = 'Kwc_Basic_Svg_Model';
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_Form';

        $ret['outputType'] = 'source'; // source | url
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        $uploadRow = $ret['row']->getParentRow('Upload');
        $ret['outputType'] = $this->_getSetting('outputType');

        if ($uploadRow) {
            if ($ret['outputType'] === 'source') {
                $fileSource = $uploadRow->getFileSource();
                if (file_exists($fileSource)) {
                    $ret['fileSource'] = file_get_contents($fileSource);
                }
            } else if ($ret['outputType'] === 'url') {
                $filename = $uploadRow->filename . '.' . $uploadRow->extension;
                $url = Kwf_Media::getUrl($this->getData()->componentClass, $this->getData()->componentId, 'default', $filename);
                $ev = new Kwf_Component_Event_CreateMediaUrl($this->getData()->componentClass, $this->getData(), $url);
                Kwf_Events_Dispatcher::fireEvent($ev);
                $ret['fileUrl'] = $ev->url;
            }
        }

        return $ret;
    }

    public static function getMediaOutput($id, $type, $className)
    {
        if ($type !== 'default') return null;

        $component = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible' => true));
        if (!$component) return null;

        $row = $component->getComponent()->getRow();
        if (!$row) return null;

        $uploadRow = $row->getParentRow('Upload');
        if (!$uploadRow) return null;

        return array(
            'file' => $uploadRow->getFileSource(),
            'mimeType' => $uploadRow->mime_type
        );
    }

    public function hasContent()
    {
        return !!$this->getRow()->kwf_upload_id;
    }
}
