<?php
class Kwf_Controller_Action_Redirects_RedirectController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Kwf_Util_Model_Redirects';
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');

    public static function getDomains()
    {
        if (!Kwf_Component_Data_Root::getComponentClass()) {
            return null;
        }
        $domainComponentClasses = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (Kwc_Abstract::hasSetting($c, 'baseProperties') &&
                in_array('domain', Kwc_Abstract::getSetting($c, 'baseProperties'))
            ) {
                $domainComponentClasses[] = $c;
            }
        }
        $domains = array();
        foreach (Kwf_Component_Data_Root::getInstance()
            ->getComponentsBySameClass($domainComponentClasses, array('ignoreVisible'=>true)) as $c
        ) {
            $acl = Zend_Registry::get('acl');
            if ($acl->getComponentAcl()->isAllowed(Kwf_Registry::get('userModel')->getAuthedUser(), $c)) {
                $domains[$c->dbId] = $c->name;
            }
        }
        return $domains;
    }

    protected function _initFields()
    {
        parent::_initFields();
        $domains = self::getDomains();
        if ($domains && count($domains) > 1) {
            $this->_form->add(new Kwf_Form_Field_Select('domain_component_id', trlKwf('Domain')))
                ->setWidth(150)
                ->setValues($domains)
                ->setAllowBlank(false);
        }

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

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        $domains = self::getDomains();
        if ($domains && count($domains) == 1) {
            $row->domain_component_id = array_pop(array_keys($domains));
        }
    }

    protected function _hasPermissions($row, $action)
    {
        $ret = parent::_hasPermissions($row, $action);

        $acl = Zend_Registry::get('acl');
        if ($ret) {
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($row->domain_component_id, array('limit'=>1)) as $d) {
                if (!$acl->getComponentAcl()->isAllowed(Kwf_Registry::get('userModel')->getAuthedUser(), $d)) {
                    $ret = false;
                    break;
                }
            }
        }
        return $ret;
    }
}
