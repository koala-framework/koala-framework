<?php
class Kwc_Paragraphs_Admin extends Kwc_Admin
{
    public function componentToString($c)
    {
        return Kwf_Trl::getInstance()->trlStaticExecute(Kwc_Abstract::getSetting($c->componentClass, 'componentName'));
    }

    //gibts nimma
    protected final function _getComponents()
    {
    }

    public function setup()
    {
        $tablename = 'kwc_paragraphs';
        if (!$this->_tableExists($tablename)) {
            Kwf_Registry::get('db')->query("CREATE TABLE `$tablename` (
                  `id` int(10) unsigned NOT NULL auto_increment,
                  `component_id` varchar(255) NOT NULL,
                  `component` varchar(255) NOT NULL,
                  `pos` smallint NOT NULL,
                  `visible` tinyint(4) NOT NULL
                   PRIMARY KEY  (`id`)
                     ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }
    }
}
