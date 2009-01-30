<?php
class Vpc_TextImage_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Text-Image');
        $ret['modelname'] = 'Vpc_TextImage_Model';
        $ret['generators']['child']['component']['text'] = 'Vpc_Basic_Text_Component';
        $ret['generators']['child']['component']['image'] = 'Vpc_TextImage_ImageEnlarge_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $this->getRow();
        if (!$row->image) {
            $ret['image'] = false;
        } else {
            $pos = $row->position;
            if ($pos == 'alternate') {
                $pos = 'left'; //TODO
            }
            $ret['propCssClass'] = 'position'.ucfirst($pos);
            if ($row->flow) {
                $ret['propCssClass'] .= ' flow';
            } else {
                $ret['propCssClass'] .= ' noFlow';
            }
        }
        return $ret;
    }
}
