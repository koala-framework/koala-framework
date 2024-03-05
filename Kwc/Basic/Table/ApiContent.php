<?php
class Kwc_Basic_Table_ApiContent implements Kwf_Component_ApiContent_Interface
{
    public function getContent(Kwf_Component_Data $data)
    {
        $ret = array();
        $settingsRow = $data->getComponent()->getRow();
        $ret['responsiveStyle'] = $settingsRow->responsive_style;
        $ret['tableStyle'] = $settingsRow->table_style;
        $ret['columnCount'] = $data->getComponent()->getColumnCount();

        $ret['table'] = array();
        $dataSelect = new Kwf_Model_Select();
        $dataSelect->whereEquals('visible', 1);
        $dataSelect->order('pos', 'ASC');
        $rows = $data->getComponent()->getRow()->getChildRows('tableData', $dataSelect);
        foreach ($rows as $row) {
            $columns = array();
            for($i = 0; $i < $ret['columnCount']; $i++) {
                $columns[] = $row->{'column'.($i+1)};
            }
            $ret['table'][] = array(
                'style' => $row->css_style,
                'columns' => $columns
            );
        }
        return $ret;
    }
}
