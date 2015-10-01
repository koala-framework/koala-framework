<?php
class Kwc_Box_MetaTagsContent_Trl_Admin extends Kwc_Abstract_Composite_Trl_Admin
{
    public function getPagePropertiesForm($config)
    {
        $c = $config['component'];
        $form = null;
        if ($config['mode'] == 'add' || $c->isPage) {
            $form = new Kwc_Box_MetaTagsContent_Trl_Form(null, $this->_class);
        } else if (Kwc_Abstract::getFlag($c->componentClass, 'subroot') || $c->componentId == 'root') {
            $form = new Kwc_Box_MetaTagsContent_Trl_SubrootForm(null, $this->_class);
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

    /*
    public function getPagePropertiesForm($config)
    {
        $c = $config['component'];
        $form = Kwc_Admin::getInstance(Kwc_Abstract::getSetting($this->_class, 'masterComponentClass'))
                ->getPagePropertiesForm($config);
        if ($form) {
            $form->setIdTemplate('{0}-child');
            $ret = new Kwf_Form();
            $ret->setModel(new Kwf_Model_FnF());
            $ret->setCreateMissingRow(true);
            $ret->add($form);
            return $ret;
        }
        return null;
    }
    */
}
