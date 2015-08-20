<?php
class Kwf_Update_20150819UpdatePageFilenames extends Kwf_Update
{
    protected $_tags = array('kwc');

    public function update()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwc_Root_Category_GeneratorModel');
        foreach ($model->getRows() as $row) {
            $page = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($row->id, array('ignoreVisible' => true));
            if ($page) {
                $row->save();
            }
        }
    }
}
