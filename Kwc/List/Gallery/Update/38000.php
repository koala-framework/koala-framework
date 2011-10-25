<?php
class Kwc_List_Gallery_Update_38000 extends Kwf_Update
{
    public function update()
    {
        //das wird hoffentlich schnell genug sein...
        $galleries = Kwf_Component_Data_Root::getInstance()->getComponentsByClass('Kwc_List_Gallery_Component');
        echo "\nUpdating ".count($galleries)." galleries...\n";
        $variants = array();
        foreach ($galleries as $g) {
            $row = $g->getComponent()->getRow();
            $row->columns = substr($row->variant, 0, 1);
            if (!in_array($row->variant, $variants)) {
                echo "$row->variant -> $row->columns\n";
            }
            $row->save();
        }
    }
}
