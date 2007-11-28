<?php
class Vpc_Basic_Link_Intern_Form extends Vps_Auto_Vpc_Form
{
    public function __construct($class, $pageId = null, $componentKey = null)
    {
        parent::__construct($class, $pageId, $componentKey);

        $this->add(new Vps_Auto_Field_TextField('rel', 'Rel'))
            ->setWidth(500);

        $this->add(new Vps_Auto_Field_TextField('param', 'Parameters'))
            ->setWidth(500);

        $this->add(new Vpc_Basic_Link_Intern_Field('target', 'Target'))
            ->setWidth(500)
            ->setControllerUrl(Vpc_Admin::getInstance($class)->getControllerUrl('Vpc_Basic_Link_Intern_Pages'));
    }
    
    public function prepareSave($row, $postData)
    {
        $pageId = $row->page_id;
        if ($pageId == $postData[$this->fields['target']->getFieldName()]) {
            throw new Vps_ClientException('Link cannot link to itself');
        }
        parent::prepareSave($row, $postData);
    }
}