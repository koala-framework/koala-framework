<?php
class Kwc_Root_Category_Trl_Update_20150819UpdatePageTrlFilenames extends Kwf_Update
{
    public function update()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_Trl_GeneratorModel');
        foreach ($model->getRows() as $row) {
            $row->save();
        }
    }
}
