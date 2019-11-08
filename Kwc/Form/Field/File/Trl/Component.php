<?php
class Kwc_Form_Field_File_Trl_Component extends Kwc_Form_Field_Abstract_Trl_Component
{
    public function getSubmitMessage($row)
    {
        $message = '';
        if ($this->getFormField()->getFieldLabel()) {
            $message .= $this->getFormField()->getFieldLabel().': ';
        }
        $uploadRow = $row->getParentRow($this->getFormField()->getName());
        if ($uploadRow) {
            $row->addAttachment($uploadRow);
            $message .= "{$uploadRow->filename}.{$uploadRow->extension} "
                        .$this->getData()->trlKwf('attached');
        }
    }
}
