<?php
class Kwf_Controller_Action_Redirects_RedirectController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Util_Model_Redirects';
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->add(new Kwf_Form_Field_Select('type', trlKwf('Type')))
            ->setValues(array(
                'path' => trlKwf('Path'),
                'domain' => trlKwf('Domain'),
                'domainPath' => trlKwf('Domain and Path'),
            ))
            ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('source', trlKwf('Source')))
            ->setAllowBlank(false);
        if (Kwf_Registry::get('acl') instanceof Kwf_Acl_Component) {
            $this->_form->add(new Kwf_Form_Field_PageSelect('target', trlKwf('Target')))
                ->setControllerUrl('/kwf/redirects/pages')
                ->setAllowBlank(false);
        } else {
            $this->_form->add(new Kwf_Form_Field_TextField('target', trlKwf('Target')))
                ->setAllowBlank(false);
        }
        $this->_form->add(new Kwf_Form_Field_TextField('comment', trlKwf('Comment')));
    }
}
