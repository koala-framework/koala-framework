<?php
class Kwc_Box_MetaTagsContent_Admin extends Kwc_Abstract_Admin
{
    public function getPagePropertiesForm()
    {
        $form = new Kwc_Box_MetaTagsContent_Form(null, $this->_class);
        $fs = new Kwf_Form_Container_FieldSet(trlKwf('Meta Tags'));
        $fs->setCollapsible(true);
        $fs->setCollapsed(true);
        foreach ($form as $f) {
            $form->fields->remove($f);
            $fs->add($f);
        }
        $form->add($fs);
        return $form;
    }
}
