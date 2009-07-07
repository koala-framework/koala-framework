<?php
class Vpc_Newsletter_Detail_Form extends Vpc_Abstract_Form
{
    protected $_modelName = 'Vpc_Newsletter_Model';

    protected function _initFields()
    {
        parent::_initFields();

        $form = Vpc_Abstract_Form::createChildComponentForm('Vpc_Newsletter_Detail_Component', '-mail');
        $form->setIdTemplate('{component_id}_{id}-mail');
        $this->add($form);

        $this->add(new Vps_Form_Field_ShowField('create_date', trlVps('Creation Date')))
            ->setWidth(300);
    }

    /*
     * id ist komplette componentId, aber row wird nur per letzten Teil der id
     * geholt
     */
    public function getRow($parentRow = null)
    {
        $id = $this->getId();
        $pos = strpos($id, '_');
        if ($pos) {
            $id = (int)substr($id, $pos + 1);
            $row = $this->_model->getRow($id);
        } else {
            $row = $this->_model->createRow();
            $row->component_id = $id;
        }
        return $row;
    }
}
