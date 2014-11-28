<?php
class Kwc_SocialMedia_2ClickButtons_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwfStatic('Social Media')));

        $fs->add(new Kwf_Form_Field_Checkbox('facebook', trlKwfStatic('Facebook')))
            ->setHideLabel(true)
            ->setBoxLabel(trlKwfStatic('Facebook'));
        $fs->add(new Kwf_Form_Field_Checkbox('twitter', trlKwfStatic('Twitter')))
            ->setHideLabel(true)
            ->setBoxLabel(trlKwfStatic('Twitter'));
        $fs->add(new Kwf_Form_Field_Checkbox('google', trlKwfStatic('Google+')))
            ->setHideLabel(true)
            ->setBoxLabel(trlKwfStatic('Google+'));
    }
}

