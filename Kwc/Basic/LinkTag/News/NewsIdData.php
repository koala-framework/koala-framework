<?php
class Kwc_Basic_LinkTag_News_NewsIdData extends Kwf_Data_Table
{
    public function load($row)
    {
        $ret = parent::load($row);
        if (!$ret) return $ret;
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId('news_'.$ret, array('ignoreVisible'=>true, 'limit'=>1));
        $ret = array(
            'name' => $c ? $c->name : $ret,
            'id' => $ret
        );
        return $ret;
    }
}
