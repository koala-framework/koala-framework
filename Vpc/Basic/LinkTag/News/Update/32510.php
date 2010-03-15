<?php
class Vpc_Basic_LinkTag_News_Update_32510 extends Vps_Update
{
    protected $_tags = array('vpc');

    public function update()
    {
        parent::update();

        $db = Zend_Registry::get('db');
        $db->query("CREATE TABLE IF NOT EXISTS `vpc_basic_link_news` (
  `component_id` varchar(255) NOT NULL,
  `news_id` varchar(255) NOT NULL,
  PRIMARY KEY  (`component_id`),
  KEY `news_id` (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $fieldModel = Vps_Model_Abstract::getInstance('Vps_Component_FieldModel');
        $rows = $fieldModel->getRows($fieldModel->select()
            ->where(new Vps_Model_Select_Expr_Like('data', '%news_id%'))
        );

        $newsTagModel = Vps_Model_Abstract::getInstance('Vpc_Basic_LinkTag_News_Model');
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
