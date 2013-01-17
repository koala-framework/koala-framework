<?php
class Kwf_Update_36008 extends Kwf_Update
{
    public function update()
    {
        echo "Deleting view cache...";
        $select = new Kwf_Model_Select();
        Kwf_Component_Cache::getInstance()->deleteViewCache($select);
        echo "done\n";
    }
}
