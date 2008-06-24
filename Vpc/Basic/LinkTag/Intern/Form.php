<?php
class Vpc_Basic_LinkTag_Intern_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vpc_Basic_LinkTag_Intern_Field('target', 'Target'))
            ->setWidth(500)
            ->setControllerUrl(Vpc_Admin::getInstance($class)->getControllerUrl('Vpc_Basic_LinkTag_Intern_Pages'));
    }

    public function prepareSave($parentRow, $postData)
    {
        $pageId = Vps_Component_Data_Root::getInstance()->getByDbId($parentRow->component_id)
            ->getPage()->dbId;
        if ($this->fields['target']->getSave() &&
                $pageId == $postData[$this->fields['target']->getFieldName()]) {
            throw new Vps_ClientException(trlVps('Link cannot link to itself'));
        }
        parent::prepareSave($parentRow, $postData);
    }
}
