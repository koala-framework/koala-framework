<?php
class Vpc_Basic_LinkTag_Event_Update_32511 extends Vps_Update
{
    protected $_tags = array('vpc');

    public function update()
    {
        parent::update();

        $db = Zend_Registry::get('db');
        $db->query("CREATE TABLE IF NOT EXISTS `vpc_basic_link_event` (
  `component_id` varchar(255) NOT NULL,
  `event_id` varchar(255) NOT NULL,
  PRIMARY KEY  (`component_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $fieldModel = Vps_Model_Abstract::getInstance('Vps_Component_FieldModel');
        $rows = $fieldModel->getRows($fieldModel->select()
            ->where(new Vps_Model_Select_Expr_Like('data', '%event_id%'))
        );

        $eventTagModel = Vps_Model_Abstract::getInstance('Vpc_Basic_LinkTag_Event_Model');
        foreach ($rows as $row) {
            $eventTagModel->createRow(array(
                'component_id' => $row->component_id,
                'event_id'      => $row->event_id
            ))->save();
        }
    }
}
