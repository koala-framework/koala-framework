<?php
class Kwc_Box_MetaTagsContent_Admin extends Kwc_Abstract_Admin
{
    public function getPagePropertiesForm($config)
    {
        $c = $config['component'];
        $form = null;
        if ($config['mode'] == 'add' || $c->isPage) {
            $form = new Kwc_Box_MetaTagsContent_Form(null, $this->_class);
        } else if (Kwc_Abstract::getFlag($c->componentClass, 'subroot') || $c->componentId == 'root') {
            $form = new Kwc_Box_MetaTagsContent_SubrootForm(null, $this->_class);
        }
        if ($form) {
            $fs = new Kwf_Form_Container_FieldSet(trlKwf('SEO, Open Graph, Sitemap'));
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

    protected function _duplicateOwnRow($source, $target)
    {
        $ret = parent::_duplicateOwnRow($source, $target);

        //these meta tags should not get duplicated
        $ret->description = null;
        $ret->og_title = null;
        $ret->og_description = null;

        $ret->save();
        return $ret;
    }
}
