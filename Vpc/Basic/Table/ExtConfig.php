<?php
class Vpc_Basic_Table_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $settings = $this->_getStandardConfig('vps.autoform', 'Settings', trlVps('Settings'), new Vps_Asset('wrench'));

        $table = $this->_getStandardConfig('vpc.tablegridpanel', 'Index', trlVps('Table'), new Vps_Asset('application_view_columns'));
        $table['renderAlways'] = true;
        $table['insertNewRowAtBottom'] = true;

        $xlsImportTable = $this->_getStandardConfig('vpc.tablexlsimport', 'Import', trlVps('XLS Import'), new Vps_Asset('page_excel'));

        return array(
            'table' => $table,
            'xlsImportTable' => $xlsImportTable,
            'settings' => $settings
        );
    }

    public function getEditAfterCreateConfigKey()
    {
        return 'settings';
    }
}
