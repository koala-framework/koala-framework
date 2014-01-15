<?php
class Kwc_Basic_LinkTag_News_NewsIdData extends Kwf_Data_Table
{
    public function load($row)
    {
        $ret = parent::load($row);
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId('news_'.$ret, array('ignoreVisible'=>true, 'limit'=>1));
        $ret = array(
            'name' => $c->name,
            'id' => $ret
        );
        return $ret;
    }
}
