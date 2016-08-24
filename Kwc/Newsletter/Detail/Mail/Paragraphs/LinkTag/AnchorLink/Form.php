<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_AnchorLink_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Kwf_Form_Field_Select('anchor', trlKwf('Anchor')))
            ->setValues(Kwc_Admin::getInstance($class)->getControllerUrl('Anchors') . '/json-data')
            ->setShowNoSelection(true);
    }
}
