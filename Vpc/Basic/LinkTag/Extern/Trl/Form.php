<?php
class Vpc_Basic_LinkTag_Extern_Trl_Form_OriginalData extends Vps_Data_Abstract
{
    public function load($row)
    {
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible'=>true));
        // das ist nötig weil bei der übersetzung bei den link-cards
        // natürlich gleich alle geladen werden und im chained dann zB ein
        // download-tag drin ist und kein externer / etc.
        if (is_instance_of($c->chained->componentClass, 'Vpc_Basic_LinkTag_Extern_Component')) {
            return $c->chained
                ->getComponent()
                ->getRow()
                ->target;
        } else {
            return '';
        }
    }
}

class Vpc_Basic_LinkTag_Extern_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $this->add(new Vps_Form_Field_TextField('target', trlVps('Url')))
            ->setWidth(450)
            ->setAllowBlank(false)
            ->setVtype('url');
        $this->add(new Vps_Form_Field_ShowField('original', trlVps('Original')))
            ->setData(new Vpc_Basic_LinkTag_Extern_Trl_Form_OriginalData());
    }
}
