<?php
class Kwc_TextImage_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Text-Image');
        $ret['componentCategory'] = 'content';
        $ret['componentPriority'] = 100;
        $ret['ownModel'] = 'Kwc_TextImage_Model';
        $ret['generators']['child']['component']['text'] = 'Kwc_Basic_Text_Component';
        $ret['generators']['child']['component']['image'] = 'Kwc_TextImage_ImageEnlarge_Component';
        $ret['mailImageVAlign'] = 'center'; // valign von Image
        $ret['apiContent'] = 'Kwc_TextImage_ApiContent';
        $ret['apiContentType'] = 'textImage';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $row = $this->getRow();
        $ret['row'] = $row;
        if (!$row->image) {
            $ret['image'] = false;
        } else {
            $ret['rootElementClass'] .= ' '.$this->_getBemClass('--imageDimension'.ucfirst($ret['image']->getComponent()->getDimensionSetting()));
            $dim = $ret['image']->getComponent()->getImageDimensions();
            $ret['imageWidth'] = false;
            if ($dim && isset($dim['width'])) {
                $ret['imageWidth'] = $dim['width'];
            }
            $ret['position'] = $row->position;
            $ret['rootElementClass'] .= ' '.$this->_getBemClass('--position'.ucfirst($row->position));
            $ret['rootElementClass'] .= ' '.$this->_getBemClass('--'.($row->flow ? 'flow' : 'noFlow'));
            if ($ret['imageWidth'] <= 100) {
                $ret['rootElementClass'] .= ' '.$this->_getBemClass('--smallImage');
            }
            $ret['mailImageVAlign'] = $this->_getSetting('mailImageVAlign');
        }
        if (!$ret['text']->hasContent()) {
            $ret['rootElementClass'] .= ' '.$this->_getBemClass('--noText');
        }
        return $ret;
    }
}
