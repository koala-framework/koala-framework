<?php
class Kwf_Component_Generator_Categories_Category extends Kwc_Root_Category_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = 'Kwf_Component_Generator_Categories_PagesModel';
        $ret['generators']['page']['historyModel'] = 'Kwf_Component_Generator_Categories_HistoryModel';
        $ret['generators']['page']['component'] = array(
            'empty' => 'Kwc_Basic_None_Component'
        );
        return $ret;
    }
}
?>
