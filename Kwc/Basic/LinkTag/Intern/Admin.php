<?php
class Kwc_Basic_LinkTag_Intern_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
    implements Kwf_Component_Abstract_Admin_Interface_DependsOnRow
{
    public function getComponentsDependingOnRow(Kwf_Model_Row_Interface $row)
    {
        // nur bei pageModel
        if ($row->getModel() instanceof Kwc_Root_Category_GeneratorModel) {
            $linkModel = Kwf_Model_Abstract::getInstance(
                Kwc_Abstract::getSetting($this->_class, 'ownModel')
            );
            $linkingRows = $linkModel->getRows($linkModel->select()
                ->whereEquals('target', $row->{$row->getModel()->getPrimaryKey()})
            );
            if (count($linkingRows)) {
                $ret = array();
                foreach ($linkingRows as $linkingRow) {
                    $c = Kwf_Component_Data_Root::getInstance()
                        ->getComponentByDbId($linkingRow->component_id);
                    //$c kann null sein wenn es nicht online ist
                    if ($c) $ret[] = $c;
                }
                return $ret;
            }
        }
        return array();
    }

    public function componentToString(Kwf_Component_Data $data)
    {
        if (!$data->getLinkedData()) return '';
        return $data->getLinkedData()->name;
    }
}
