<?php
class Kwc_Newsletter_Subscribe_FrontendForm extends Kwf_Form
{
    protected $_modelName = 'Kwc_Newsletter_Subscribe_Model';
    protected $_subscribeComponentId;
    protected $_newsletterComponentId;

    public function __construct($name, $componentClass, $subscribeOrNewsletterComponentId)
    {
        if ($subscribeOrNewsletterComponentId) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
                $subscribeOrNewsletterComponentId, array('ignoreVisible' => true)
            );
            if (is_instance_of($c->componentClass, 'Kwc_Newsletter_Component')) {
                $this->_newsletterComponentId = $subscribeOrNewsletterComponentId;
            } else if (is_instance_of($c->componentClass, 'Kwc_Newsletter_Subscribe_Component')) {
                $this->_subscribeComponentId = $subscribeOrNewsletterComponentId;
                $this->_newsletterComponentId = $c->getComponent()->getSubscribeToNewsletterComponent()->dbId;
            } else {
                throw new Kwf_Exception("component '$subscribeOrNewsletterComponentId' is not a newsletter or a newsletter_subscribe component");
            }
        }
        parent::__construct($name);
    }

    protected function _addEmailValidator()
    {
        $validator = new Kwc_Newsletter_Subscribe_EmailValidator($this->_newsletterComponentId);
        $this->fields['email']->addValidator($validator, 'email');
    }

    protected function _initFields()
    {
        parent::_initFields();

        $this->add(new Kwf_Form_Field_Radio('gender', trlKwfStatic('Gender')))
            ->setAllowBlank(false)
            ->setValues(array(
                'female' => trlKwfStatic('Female'),
                'male'   => trlKwfStatic('Male')
            ))
            ->setCls('kwf-radio-group-transparent');
        $this->add(new Kwf_Form_Field_TextField('title', trlKwfStatic('Title')));
        $this->add(new Kwf_Form_Field_TextField('firstname', trlKwfStatic('Firstname')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('lastname', trlKwfStatic('Lastname')))
            ->setAllowBlank(false);
        $this->add(new Kwf_Form_Field_TextField('email', trlKwfStatic('E-Mail')))
            ->setVtype('email')
            ->setAllowBlank(false);
        $this->_addEmailValidator();
    }
}
