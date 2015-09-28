<?php
class Kwc_ParagraphsPassword_Component extends Kwc_Paragraphs_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Paragraphs passwordprotected');
        $ret['extConfig'] = 'Kwc_ParagraphsPassword_ExtConfig';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['plugins']['password'] = 'Kwc_ParagraphsPassword_Plugin_Component';

        $ret['flags']['skipFulltext'] = true;
        return $ret;
    }

    public function getPassword()
    {
        return $this->_getRow()->password;
    }
}
