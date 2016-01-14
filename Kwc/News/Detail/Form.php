<?php
class Kwc_News_Detail_Form extends Kwc_News_Detail_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add($this->_createChildComponentForm('-image'));

        $fs = $this->add(new Kwf_Form_Container_FieldSet(trlKwf('SEO, Open Graph, Sitemap')));
        $fs->setName('customMetaTagsFieldSet');
        $fs->setCollapsible(true);
        $fs->setCollapsed(true);
        $fs->add($this->_createChildComponentForm('-customMetaTags'));
    }
}
