<?php
class Kwc_Basic_Text_Events extends Kwc_Abstract_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => Kwc_Abstract::getSetting($this->_class, 'stylesModel'),
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onStylesRowUpdate'
        );
        return $ret;
    }

    public function onStylesRowUpdate(Kwf_Events_Event_Row_Updated $e)
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('uses_styles', true);
        $s->where(new Kwf_Model_Select_Expr_Like('content', '%class="style'.$e->row->id.'"%'));
        $rows = Kwc_Basic_Text_Component::createOwnModel($this->_class)
            ->export(Kwf_Model_Interface::FORMAT_ARRAY, $s, array('columns'=>array('component_id')));
        foreach ($rows as $row) {
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($row['component_id']) as $c) {
                if ($c->componentClass == $this->_class) {
                    $this->fireEvent(new Kwf_Component_Event_Component_ContentChanged(
                        $this->_class, $c
                    ));
                }
            }
        }
    }
}
