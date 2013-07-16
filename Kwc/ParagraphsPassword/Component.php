<?php
class Kwc_ParagraphsPassword_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwf('Paragraphs passwordprotected');
        $ret['extConfig'] = 'Kwc_ParagraphsPassword_ExtConfig';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['plugins'][] = 'Kwc_ParagraphsPassword_Plugin_Component';
        return $ret;
    }

    public function getPassword()
    {
        return $this->_getRow()->password;
    }
}
