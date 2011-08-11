<?php
class Vpc_Basic_LinkTag_News_Form extends Vpc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vps_Form_Field_Select('news_id', trlVps('News')))
            ->setDisplayField('title')
            ->setPageSize(20)
            ->setStoreUrl(
                Vpc_Admin::getInstance($class)->getControllerUrl('News').'/json-data'
            )
            ->setWidth(300)
            ->setListWidth(317)
            ->setAllowBlank(false);
    }

    public function getIsCurrentLinkTag($parentRow)
    {
        $row = $this->getRow($parentRow);
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId('news_'.$row->news_id, array('ignoreVisible'=>true));
        return 'news_'.$c->parent->dbId == $this->getName();
    }
}
