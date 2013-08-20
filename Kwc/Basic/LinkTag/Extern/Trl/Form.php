<?php
class Kwc_Basic_LinkTag_Extern_Trl_Form_OriginalData extends Kwf_Data_Abstract
{
    public function load($row)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($row->component_id, array('ignoreVisible'=>true));
        // das ist nötig weil bei der übersetzung bei den link-cards
        // natürlich gleich alle geladen werden und im chained dann zB ein
        // download-tag drin ist und kein externer / etc.
        if ($c && is_instance_of($c->chained->componentClass, 'Kwc_Basic_LinkTag_Extern_Component')) {
            return $c->chained
                ->getComponent()
                ->getRow()
                ->target;
        } else {
            return '';
        }
    }
}

class Kwc_Basic_LinkTag_Extern_Trl_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        parent::_initFields();
        $enableTranslation = $this->add(new Kwf_Form_Container_FieldSet(
                        trlStatic('Own Link')));
        $enableTranslation->setCheckboxToggle(true);
        $enableTranslation->setCheckboxName('own_target');

        $enableTranslation->add(new Kwf_Form_Field_TextField('target', trlKwf('Url')))
            ->setWidth(450)
            ->setVtype('url');
        $this->add(new Kwf_Form_Field_ShowField('original', trlKwf('Original')))
            ->setData(new Kwc_Basic_LinkTag_Extern_Trl_Form_OriginalData());
    }
}
