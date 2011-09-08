<?php
class Vpc_Box_MetaTagsContent_Admin extends Vpc_Abstract_Admin
{
    public function getPagePropertiesForm()
    {
        $form = new Vpc_Box_MetaTagsContent_Form(null, $this->_class);
        $fs = new Vps_Form_Container_FieldSet(trlVps('Meta Tags'));
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
