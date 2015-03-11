<?php
class Kwc_Basic_LinkTag_News_Update_20150309Legacy32510 extends Kwf_Update
{
    protected $_tags = array('kwc');

    public function update()
    {
        parent::update();

        $db = Zend_Registry::get('db');
        $db->query("CREATE TABLE IF NOT EXISTS `kwc_basic_link_news` (
  `component_id` varchar(255) NOT NULL,
  `news_id` varchar(255) NOT NULL,
  PRIMARY KEY  (`component_id`),
  KEY `news_id` (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $fieldModel = Kwf_Model_Abstract::getInstance('Kwf_Component_FieldModel');
        $rows = $fieldModel->getRows($fieldModel->select()
            ->where(new Kwf_Model_Select_Expr_Like('data', '%news_id%'))
        );

        $newsTagModel = Kwf_Model_Abstract::getInstance('Kwc_Basic_LinkTag_News_Model');
        foreach ($rows as $row) {
            if ($row->news_id) {
                $newsTagModel->createRow(array(
                    'component_id' => $row->component_id,
                    'news_id'      => $row->news_id
                ))->save();
            }
        }
    }
}
