<?php
/**
 * Editable mail that automatically creates entry in Menu for editing itself
 *
 * Useful for mails that are deep in the component tree and thus not editable through pages admin.
 */
class Kwc_Mail_Editable_Component extends Kwc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasResources'] = true;
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Mail/Editable/Panel.js';
        return $ret;
    }

    // Wird hier verwendet: Kwc_Mail_Editable_ComponentsModel
    public function getNameForEdit()
    {
        return Kwc_Abstract::getSetting($this->getData()->componentClass, 'componentName');
    }
}
