<?php
class Vpc_ParagraphsPassword_Component extends Vpc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Paragraphs passwordprotected');
        $ret['extConfig'] = 'Vpc_ParagraphsPassword_ExtConfig';
        $ret['ownModel'] = 'Vps_Component_FieldModel';
        $ret['plugins'][] = 'Vpc_ParagraphsPassword_Plugin_Component';
        return $ret;
    }

    public function getPassword()
    {
        return $this->_getRow()->password;
    }
}
