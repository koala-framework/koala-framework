<?php
class Kwc_Root_Category_Trl_Update_20150819UpdatePageTrlFilenames extends Kwf_Update
{
    public function postUpdate()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_Trl_GeneratorModel');
        foreach ($model->getRows() as $row) {
            $page = Kwf_Component_Data_Root::getInstance()
                ->getComponentByDbId($row->component_id, array('ignoreVisible' => true));
            if ($page) {
                $row->save();
            }
        }
    }
}
