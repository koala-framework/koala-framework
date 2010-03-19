<?php
class Vpc_Basic_LinkTag_News_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
    }
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Vps_Form_Field_Select('news_id', trlVps('News')))
            ->setDisplayField('title')
            ->setStoreUrl(
                Vpc_Admin::getInstance($class)->getControllerUrl('News').'/json-data'
            )
            ->setListWidth(210)
            ->setAllowBlank(false);
    }

    public function getIsCurrentLinkTag($parentRow)
    {
        $row = $this->getRow($parentRow);
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId('news_'.$row->news_id);
        return 'news_'.$c->parent->dbId = $this->getName();
    }
}
