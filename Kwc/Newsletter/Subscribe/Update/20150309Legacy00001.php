<?php
class Kwc_Newsletter_Subscribe_Update_20150309Legacy00001 extends Kwf_Update
{
    private $_afterUpdateRequired = false;
    public function update()
    {
        $db = Kwf_Registry::get('db');

        $sql = "CREATE TABLE IF NOT EXISTS `kwc_newsletter_subscribers` (
                `id` int(10) unsigned NOT NULL auto_increment,
                `gender` enum('','female','male') NOT NULL,
                `title` varchar(255) NOT NULL,
                `firstname` varchar(255) NOT NULL,
                `lastname` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `subscribe_date` datetime NOT NULL,
                `unsubscribed` tinyint(1) NOT NULL,
                `activated` tinyint( 1 ) NOT NULL DEFAULT '0',
                PRIMARY KEY  (`id`)
        ) ENGINE=InnoDB";
        $db->query($sql);
        $db->query("ALTER TABLE  `kwc_newsletter_subscribers` ADD  `newsletter_component_id` VARCHAR( 200 ) NOT NULL AFTER  `id`");
        $db->query("ALTER TABLE  `kwc_newsletter_subscribers` ADD INDEX (  `newsletter_component_id` )");
        if ($db->query("SELECT COUNT(*) FROM `kwc_newsletter_subscribers`")->fetchColumn()) {
            $nlCId = $db->query("SELECT component_id FROM `kwc_newsletter` LIMIT 1")->fetchColumn();
            if ($nlCId) {
                $db->query("UPDATE `kwc_newsletter_subscribers` SET newsletter_component_id='$nlCId'");
            } else {
                $this->_afterUpdateRequired = true;
            }
        }
    }

    public function postUpdate()
    {
        if ($this->_afterUpdateRequired) {
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentByClass('Kwc_Newsletter_Component', array('limit'=>1));
            Kwf_Registry::get('db')
                ->query("UPDATE `kwc_newsletter_subscribers` SET newsletter_component_id='$c->dbId'");
        }
    }
}
