<?php
class Kwf_Update_37010 extends Kwf_Update
{
    protected $_tags = array('kwc');
    public function update()
    {
        echo "Deleting view cache...";
        $select = new Kwf_Model_Select();
        Kwf_Component_Cache::getInstance()->deleteViewCache($select);
        echo "done\n";
    }
}
