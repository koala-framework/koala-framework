<?php
class Kwf_Controller_Action_Redirects_RedirectController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Util_Model_Redirects';
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');

    protected function _initFields()
    {
        parent::_initFields();
        $fs = $this->_form->add(new Kwf_Form_Container_FieldSet(trlKwf('Source')));
        $fs->add(new Kwf_Form_Field_Select('type', trlKwf('Type')))
            ->setWidth(150)
            ->setValues(array(
                'path' => trlKwf('Path'),
                'domain' => trlKwf('Domain'),
                'domainPath' => trlKwf('Domain and Path'),
            ))
            ->setAllowBlank(false);
        $fs->add(new Kwf_Form_Field_TextField('source', trlKwf('Source')))
            ->setWidth(500)
            ->setAllowBlank(false);

        $fs = $this->_form->add(new Kwf_Form_Container_FieldSet(trlKwf('Target')));
        $cards = $fs->add(new Kwf_Form_Container_Cards('target_type', trlKwf('Type')));

        if (Kwf_Registry::get('acl') instanceof Kwf_Acl_Component) {
            $card = $cards->add();
            $card->setName('intern');
            $card->fields->setFormName('intern');
            $card->setTitle(trlKwf('Internal Page'));
            $card->add(new Kwf_Form_Field_PageSelect('target', trlKwf('Target')))
                ->setControllerUrl('/kwf/redirects/pages')
                ->setAllowBlank(false);

            $card = $cards->add();
            $card->setName('downloadTag');
            $card->fields->setFormName('downloadTag');
            $card->setTitle(trlKwf('Internal Download'));
            $card->add(new Kwf_Form_Field_TextField('target', trlKwf('Component-Id')))
                ->setAllowBlank(false);
        }

        $card = $cards->add();
        $card->setName('extern');
        $card->fields->setFormName('extern');
        $card->setTitle(trlKwf('External Url'));
        $card->add(new Kwf_Form_Field_UrlField('target', trlKwf('Target')))
            ->setAllowBlank(false)
            ->setWidth(500);

        $this->_form->add(new Kwf_Form_Field_TextField('comment', trlKwf('Comment')))
            ->setWidth(500);
    }
}
