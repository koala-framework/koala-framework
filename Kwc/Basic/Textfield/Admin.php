<?php
class Kwc_Basic_Textfield_Admin extends Kwc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Kwf_Grid_Column('string', trlKwf('Content'));
        $c->setData(new Kwf_Component_Abstract_ToStringData($this->_class));
        $ret['string'] = $c;
        return $ret;
    }

    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $ownRow = $cmp->getComponent()->getRow();
        $ret['content'] = $ownRow->content;
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        $ownRow = $cmp->getComponent()->getRow();
        if (isset($data['content'])) {
            $ownRow->content = $data['content'];
        }
        $ownRow->save();
    }
}
