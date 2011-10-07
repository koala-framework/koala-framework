<?php
class Kwc_Newsletter_Detail_Form extends Kwc_Abstract_Form
{
    protected $_modelName = 'Kwc_Newsletter_Model';

    protected function _initFields()
    {
        parent::_initFields();

        $class = $this->getClass();
        if (is_instance_of($class, 'Kwc_Newsletter_Component')) {
            $class = Kwc_Abstract::getSetting($this->getClass(), 'generators');
            $class = $class['detail']['component'];
        }

        $form = Kwc_Abstract_Form::createChildComponentForm($class, '-mail');
        $form->setIdTemplate('{component_id}_{id}-mail');
        $this->add($form);

        $this->add(new Kwf_Form_Field_ShowField('create_date', trlKwf('Creation Date')))
            ->setWidth(300);
    }

    /*
     * id ist komplette componentId, aber row wird nur per letzten Teil der id
     * geholt
     */
    public function getRow($parentRow = null)
    {
        $componentId = $this->getId();
        if (preg_match('/_([0-9]+)$/', $componentId, $matches)) {
            return $this->_model->getRow($matches[1]);
        } else {
            return $this->_model->createRow(array(
                'component_id' => $componentId
            ));
        }
    }
}
