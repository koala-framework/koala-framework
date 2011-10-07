<?php
class Vpc_Basic_LinkTag_Intern_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vps_Form_Field_PageSelect('target', trlVps('Target')))
            ->setControllerUrl(Vpc_Admin::getInstance($class)->getControllerUrl('Pages'))
            ->setWidth(233)
            ->setAllowBlank(false);
    }

    public function prepareSave($parentRow, $postData)
    {
        if ($parentRow) {
            // Limit 1 weil alle komponenten die hier zurück kommen, dieselbe url
            // haben und es wird ja überprüft ob zu sich selbst gelinkt wird
            $data = Vps_Component_Data_Root::getInstance()->getComponentByDbId(
                $parentRow->component_id, array('limit' => 1)
            );
            if (isset($postData[$this->fields['target']->getFieldName()]) &&
                    $data && $data->getPage() && $data->getPage()->dbId == $postData[$this->fields['target']->getFieldName()]) {
                throw new Vps_ClientException(trlVps('Link cannot link to itself'));
            }
        }
        parent::prepareSave($parentRow, $postData);
    }
}
