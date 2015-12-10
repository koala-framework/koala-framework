<?php
class Kwc_Basic_LinkTag_News_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $this->add(new Kwf_Form_Field_Select('news_id', trlKwf('News')))
            ->setDisplayField('title')
            ->setPageSize(20)
            ->setStoreUrl(
                Kwc_Admin::getInstance($class)->getControllerUrl('News').'/json-data'
            )
            ->setWidth(300)
            ->setListWidth(317)
            ->setAllowBlank(false);
    }

    public function getIsCurrentLinkTag($parentRow)
    {
        $row = $this->getRow($parentRow);
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId('news_'.$row->news_id, array('ignoreVisible'=>true));
        if ($c) {
            return 'news_'.$c->parent->dbId == $this->getName();
        } else {
            return false;
        }
    }
}
