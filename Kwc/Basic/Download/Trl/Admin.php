<?php
class Kwc_Basic_Download_Trl_Admin extends Kwc_Abstract_Composite_Trl_Admin
{
    public function gridColumns()
    {
        $ret = parent::gridColumns();
        $c = new Kwf_Grid_Column('infotext', trlKwf('Descriptiontext'));
        $c->setData(new Kwf_Data_Kwc_Table(Kwc_Abstract::getSetting($this->_class, 'ownModel'), 'infotext', $this->_class));
        $c->setEditor(new Kwf_Form_Field_TextField());
        $ret['infotext'] = $c;
        return $ret;
    }

    public function componentToString(Kwf_Component_Data $data)
    {
        return $data->getComponent()->getRow()->infotext;
    }

    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $trlRow = $cmp->getComponent()->getRow();
        $masterRow = $cmp->chained->getComponent()->getRow();
        $ret['infotext'] = $trlRow->infotext ? $trlRow->infotext : $masterRow->infotext;
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        $ownRow = $cmp->getComponent()->getRow();
        if (isset($data['infotext'])) {
            $ownRow->infotext = $data['infotext'];
        }
        $ownRow->save();
    }
}
