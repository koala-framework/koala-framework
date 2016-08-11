<?php
class Kwc_Basic_Text_ContentData extends Kwf_Data_Table
{
    public function load($row)
    {
        $content = parent::load($row);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible'=>true, 'limit'=>1));
        if ($c) {
            $content = Kwf_Trl::getInstance()->trlStaticExecute($content, $c->getLanguage());
        }
        $ret = array('componentId' => $row->component_id,
                     'content'       => $content);
        return $ret;
    }
}

