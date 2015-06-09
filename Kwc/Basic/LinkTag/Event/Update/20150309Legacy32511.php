<?php
class Kwc_Basic_LinkTag_Event_Update_20150309Legacy32511 extends Kwf_Update
{
    protected $_tags = array('kwc');

    public function update()
    {
        parent::update();

        $db = Zend_Registry::get('db');
        $db->query("CREATE TABLE IF NOT EXISTS `kwc_basic_link_event` (
  `component_id` varchar(255) NOT NULL,
  `event_id` varchar(255) NOT NULL,
  PRIMARY KEY  (`component_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $fieldModel = Kwf_Model_Abstract::getInstance('Kwf_Component_FieldModel');
        $rows = $fieldModel->getRows($fieldModel->select()
            ->where(new Kwf_Model_Select_Expr_Like('data', '%event_id%'))
        );

        $eventTagModel = Kwf_Model_Abstract::getInstance('Kwc_Basic_LinkTag_Event_Model');
        foreach ($rows as $row) {
            $eventTagModel->createRow(array(
                'component_id' => $row->component_id,
                'event_id'      => $row->event_id
            ))->save();
        }
    }
}
