<?php
class Vpc_Basic_LinkTag_News_Admin extends Vpc_Basic_LinkTag_Abstract_Admin
    implements Vps_Component_Abstract_Admin_Interface_DependsOnRow
{
    public function componentToString(Vps_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $data = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId('news_'.$row->news_id, array('subroot' => $data));
        if (!$data) return '';
        return $data->name;
    }

    public function getComponentsDependingOnRow(Vps_Model_Row_Interface $row)
    {
        // nur bei newsmodel
        if ($row->getModel() instanceof Vpc_News_Directory_Model) {
            $linkModel = Vps_Model_Abstract::getInstance(
                Vpc_Abstract::getSetting($this->_class, 'ownModel')
            );
            $linkingRows = $linkModel->getRows($linkModel->select()
                ->whereEquals('news_id', $row->{$row->getModel()->getPrimaryKey()})
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
        $fields['news_id']   = "varchar(255) NOT NULL";
        $this->createFormTable('vpc_basic_link_news', $fields);
    }
}
