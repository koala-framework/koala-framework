<?php
class Kwc_Basic_LinkTag_Intern_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Kwf_Form_Field_PageSelect('target', trlKwf('Target')))
            ->setControllerUrl(Kwc_Admin::getInstance($class)->getControllerUrl('Pages'))
            ->setWidth(233)
            ->setAllowBlank(false);

        $this->add(new Kwf_Form_Field_Select('anchor', trlKwf('Anchor')))
            ->setValues(Kwc_Admin::getInstance($class)->getControllerUrl('Anchors') . '/json-data')
            ->setShowNoSelection(true)
            ->setXtype('kwc.linktag.intern.anchor');
    }

    public function prepareSave($parentRow, $postData)
    {
        if ($parentRow) {
            // Limit 1 weil alle komponenten die hier zurück kommen, dieselbe url
            // haben und es wird ja überprüft ob zu sich selbst gelinkt wird
            $data = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
                $parentRow->component_id, array('limit' => 1, 'ignoreVisible' => true)
            );
            if (isset($postData[$this->fields['target']->getFieldName()]) &&
                $data && $data->isPage && $data->getPage() &&
                $data->getPage()->dbId == $postData[$this->fields['target']->getFieldName()]
            ) {
                throw new Kwf_ClientException(trlKwf('Link cannot link to itself'));
            }
        }
        parent::prepareSave($parentRow, $postData);
    }
}
