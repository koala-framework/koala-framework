<?php
class Kwc_Basic_LinkTag_Trl_Form extends Kwc_Abstract_Cards_Trl_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $fs = $this->add(new Kwf_Form_Container_FieldSet('SEO'));
            $fs->setCls('kwc-basic-linktag-seo');
            $fs->setCollapsible(true);
            $fs->setCollapsed(true);

        $fs->add(new Kwf_Form_Field_ShowField('original_title_text', trlKwf('Original {0}', trlKwf('Link Title'))))
            ->setData(new Kwf_Data_Trl_OriginalComponent('title_text'));
        $fs->add(new Kwf_Form_Field_TextField('title_text', 'Link Title')) //no trl
            ->setWidth(300)
            ->setHelpText(trlKwf('Optional. Description of the link or the link target. Some browsers show the text as a tooltip when the mouse pointer is hovering the link.'));
    }
}
