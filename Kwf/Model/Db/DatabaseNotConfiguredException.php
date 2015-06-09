<?php
class Kwf_Model_Db_DatabaseNotConfiguredException extends Kwf_Exception
{
    public function render($ignoreCli = false)
    {
        if (!Kwf_Registry::get('config')->setupFinished) {
            echo "<h1>".Kwf_Config::getValue('application.name')."</h1>\n";
            echo "<a href=\"".Kwf_Setup::getBaseUrl()."/kwf/maintenance/setup\">[start setup]</a>\n";
            exit;
        }
        parent::render($ignoreCli);
    }
}
