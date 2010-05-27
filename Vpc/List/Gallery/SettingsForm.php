<?php
class Vpc_List_Gallery_SettingsForm extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();

        $dimensions = Vpc_Abstract::getSetting($this->getClass(), 'dimensions');
        $values = array();
        foreach ($dimensions as $k => $dimension) {
            $values[$k] = $dimension['text'];
        }

        $this->fields->prepend(new Vps_Form_Field_Select('variant'))
            ->setFieldLabel(trlVps('Variant'))
            ->setValues($values)
            ->setShowNoSelection(true)
            ->setAllowBlank(false);
    }
}
