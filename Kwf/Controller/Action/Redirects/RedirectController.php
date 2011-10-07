<?php
class Vps_Controller_Action_Redirects_RedirectController extends Vps_Controller_Action_Auto_Form
{
    protected $_modelName = 'Vps_Util_Model_Redirects';
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');

    protected function _initFields()
    {
        parent::_initFields();
        $this->_form->add(new Vps_Form_Field_Select('type', trlVps('Type')))
            ->setValues(array(
                'path' => trlVps('Path'),
                'domain' => trlVps('Domain'),
                'domainPath' => trlVps('Domain and Path'),
            ))
            ->setAllowBlank(false);
        $this->_form->add(new Vps_Form_Field_TextField('source', trlVps('Source')))
            ->setAllowBlank(false);
        if (Vps_Registry::get('acl') instanceof Vps_Acl_Component) {
            $this->_form->add(new Vps_Form_Field_PageSelect('target', trlVps('Target')))
                ->setControllerUrl('/vps/redirects/pages')
                ->setAllowBlank(false);
        } else {
            $this->_form->add(new Vps_Form_Field_TextField('target', trlVps('Target')))
                ->setAllowBlank(false);
        }
        $this->_form->add(new Vps_Form_Field_TextField('comment', trlVps('Comment')));
    }
}
