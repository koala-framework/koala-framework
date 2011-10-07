<?php
class Kwc_Basic_Table_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $settings = $this->_getStandardConfig('kwf.autoform', 'Settings', trlKwf('Settings'), new Kwf_Asset('wrench'));

        $table = $this->_getStandardConfig('kwc.tablegridpanel', 'Index', trlKwf('Table'), new Kwf_Asset('application_view_columns'));
        $table['insertNewRowAtBottom'] = true;

        $xlsImportTable = $this->_getStandardConfig('kwf.import', 'Import', trlKwf('XLS Import'), new Kwf_Asset('page_excel'));

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
