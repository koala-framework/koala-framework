<?php
class Vpc_Basic_Download_Admin extends Vpc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = parent::gridColumns();
        $c = new Vps_Grid_Column('infotext', trlVps('Descriptiontext'));
        $c->setData(new Vps_Data_Vpc_Table(Vpc_Abstract::getSetting($this->_class, 'ownModel'), 'infotext', $this->_class));
        $c->setEditor(new Vps_Form_Field_TextField());
        $ret['infotext'] = $c;
        return $ret;
    }

    public function componentToString(Vps_Component_Data $data)
    {
        return $data->getComponent()->getRow()->infotext;
    }
}
