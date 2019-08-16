<?php
class Kwc_Basic_Link_Admin extends Kwc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Kwf_Grid_Column('string', trlKwf('Linktext'));
        $c->setData(new Kwf_Component_Abstract_ToStringData($this->_class));
        $ret['string'] = $c;
        $ret = array_merge($ret, parent::gridColumns());
        return $ret;
    }

    public function componentToString(Kwf_Component_Data $data)
    {
        return $data->getComponent()->getRow()->text;
    }

    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $ownRow = $cmp->getComponent()->getRow();
        $ret['text'] = $ownRow->text;
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        $ownRow = $cmp->getComponent()->getRow();
        if (isset($data['text'])) {
            $ownRow->text = $data['text'];
        }
        $ownRow->save();
    }
}
