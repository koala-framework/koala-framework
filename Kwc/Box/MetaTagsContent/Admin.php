<?php
class Kwc_Box_MetaTagsContent_Admin extends Kwc_Abstract_Admin
{
    public function getPagePropertiesForm($config)
    {
        $c = $config['component'];
        if ($config['mode'] == 'add' || $c->isPage) {
            $form = new Kwc_Box_MetaTagsContent_Form(null, $this->_class);
            $fs = new Kwf_Form_Container_FieldSet(trlKwf('SEO'));
            $fs->setCollapsible(true);
            $fs->setCollapsed(true);
            foreach ($form as $f) {
                $form->fields->remove($f);
                $fs->add($f);
            }
            $form->add($fs);
            return $form;
        }
        return null;
    }
}
