<?php
class Kwc_TextImage_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Text-Image');
        $ret['ownModel'] = 'Kwc_TextImage_Model';
        $ret['generators']['child']['component']['text'] = 'Kwc_Basic_Text_Component';
        $ret['generators']['child']['component']['image'] = 'Kwc_TextImage_ImageEnlarge_Component';
        $ret['assets']['files'][] = 'kwf/Kwc/TextImage/Component.js';
        $ret['assets']['dep'][] = 'KwfResponsiveEl';
        $ret['mailImageVAlign'] = 'center'; // valign von Image
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $this->getRow();
        $ret['row'] = $row;
        if (!$row->image) {
            $ret['image'] = false;
        } else {
            $dim = $ret['image']->getComponent()->getImageDimensions();
            $ret['imageWidth'] = false;
            if ($dim && isset($dim['width'])) {
                $ret['imageWidth'] = $dim['width'];
            }
            $ret['contentWidth'] = $this->getContentWidth();
            $pos = $row->position;
            if($pos == 'center'){
                $ret['center'] = ($ret['contentWidth'] - $ret['imageWidth']) / 2;
            }
            $ret['position'] = $pos;
            $ret['propCssClass'] = 'position'.ucfirst($pos);
            if ($row->flow) {
                $ret['propCssClass'] .= ' flow';
            } else {
                $ret['propCssClass'] .= ' noFlow';
            }
            $ret['mailImageVAlign'] = $this->_getSetting('mailImageVAlign');
        }
        if (!$ret['text']->hasContent()) {
            $ret['cssClass'] .= ' noText';
        }
        return $ret;
    }
}
