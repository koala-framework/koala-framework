<?php
class Vpc_Basic_LinkTag_Intern_Admin extends Vpc_Basic_LinkTag_Abstract_Admin
    implements Vps_Component_Abstract_Admin_Interface_DependsOnRow
{
    public function getComponentsDependingOnRow(Vps_Model_Row_Interface $row)
    {
        // nur bei pageModel
        if ($row->getModel() instanceof Vps_Component_PagesModel) {
            $linkModel = Vps_Model_Abstract::getInstance(
                Vpc_Abstract::getSetting($this->_class, 'ownModel')
            );
            $linkingRows = $linkModel->getRows($linkModel->select()
                ->whereEquals('target', $row->{$row->getModel()->getPrimaryKey()})
            );
            if (count($linkingRows)) {
                $ret = array();
                foreach ($linkingRows as $linkingRow) {
                    $ret[] = Vps_Component_Data_Root::getInstance()
                        ->getComponentById($linkingRow->component_id);
                }
                return $ret;
            }
        }
        return array();
    }

    public function setup()
    {
        $fields['target']   = "varchar(255) NOT NULL";
        $this->createFormTable('vpc_basic_link_intern', $fields);
    }
}
