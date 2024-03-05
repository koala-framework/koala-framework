<?php
class Kwc_Basic_LinkTag_News_Form extends Kwc_Abstract_Form
{
    public function __construct($name, $class, $id = null)
    {
        parent::__construct($name, $class, $id);

        $directories = new Kwf_Form_Field_Select('directory_component_id', trlKwf('Component'));
        $directories
            ->setDisplayField('name')
            ->setPageSize(20)
            ->setStoreUrl(
                Kwc_Admin::getInstance($class)->getControllerUrl('Directories').'/json-data'
            )
            ->setWidth(300)
            ->setListWidth(317)
            ->setAllowBlank(false);

        $filteredField = $this->_createFilteredField();

        $this->add(new Kwf_Form_Field_FilterField())
            ->setName('filterField')
            ->setFilterColumn('directoryComponentId')
            ->setFilteredField($filteredField)
            ->setFilterField($directories);
    }

    protected function _createFilteredField()
    {
        $news = new Kwf_Form_Field_Select('news_id', trlKwf('News'));
        $news
            ->setDisplayField('title')
            ->setPageSize(20)
            ->setStoreUrl(
                Kwc_Admin::getInstance($this->getClass())->getControllerUrl('News').'/json-data'
            )
            ->setWidth(300)
            ->setListWidth(317)
            ->setAllowBlank(false);
        return $news;
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
