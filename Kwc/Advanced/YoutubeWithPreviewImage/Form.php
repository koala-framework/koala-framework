<?php
class Kwc_Advanced_YoutubeWithPreviewImage_Form extends Kwc_Advanced_Youtube_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->add(new Kwf_Form_Container_FieldSet(trl('Vorschaubild')));
        $fs->add(self::createChildComponentForm($this->getClass(), '-previewImage'));
    }
}
