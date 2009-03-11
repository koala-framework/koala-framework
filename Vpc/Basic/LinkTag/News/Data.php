<?php
class Vpc_Basic_LinkTag_News_Data extends Vpc_Basic_LinkTag_Intern_Data
{
    protected function _getData()
    {
        if (($row = $this->_getRow()) && $row->news_id) {
            return Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId('news_'.$row->news_id, array('subroot' => $this));
        }
        return false;
    }
}
