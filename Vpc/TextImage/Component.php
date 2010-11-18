<?php
class Vpc_TextImage_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Text-Image');
        $ret['ownModel'] = 'Vpc_TextImage_Model';
        $ret['generators']['child']['component']['text'] = 'Vpc_Basic_Text_Component';
        $ret['generators']['child']['component']['image'] = 'Vpc_TextImage_ImageEnlarge_Component';
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
            $pos = $row->position;
            if ($pos == 'alternate') {
                $pos = 'left'; //TODO
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
        return $ret;
    }
}
