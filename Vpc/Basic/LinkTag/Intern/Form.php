<?php
class Vpc_Basic_LinkTag_Intern_TargetData extends Vps_Data_Table
{
    public function load($row)
    {
        $name = $this->_dataIndex;
        if (!$name) $name = $this->getFieldname();
        if (!isset($row->$name) && !is_null($row->$name)) { //scheiß php
            throw new Vps_Exception("Index '$name' doesn't exist in row.");
        }
        $ret = array('id' => $row->$name);
        $cmp = Vps_Component_Data_Root::getInstance()->getComponentByDbId(
            $ret['id'], array('ignoreVisible' => true)
        );
        if ($cmp) {
            $ret['name'] = $cmp->getTitle();
        } else {
            $ret['id'] = null;
            $ret['name'] = '';
        }
        return $ret;
    }
}

class Vpc_Basic_LinkTag_Intern_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vpc_Basic_LinkTag_Intern_Field('target', trlVps('Target')))
            ->setData(new Vpc_Basic_LinkTag_Intern_TargetData())
            ->setControllerUrl(Vpc_Admin::getInstance($class)->getControllerUrl('Pages'))
            ->setWidth(233);
    }

    public function prepareSave($parentRow, $postData)
    {
        if ($parentRow) {
            // Limit 1 weil alle komponenten die hier zurück kommen, dieselbe url
            // haben und es wird ja überprüft ob zu sich selbst gelinkt wird
            $data = Vps_Component_Data_Root::getInstance()->getComponentByDbId(
                $parentRow->component_id, array('limit' => 1)
            );
            if ($this->fields['target']->getInternalSave() && isset($postData[$this->fields['target']->getFieldName()]) &&
                    $data && $data->getPage() && $data->getPage()->dbId == $postData[$this->fields['target']->getFieldName()]) {
                throw new Vps_ClientException(trlVps('Link cannot link to itself'));
            }
        }
        parent::prepareSave($parentRow, $postData);
    }
}
