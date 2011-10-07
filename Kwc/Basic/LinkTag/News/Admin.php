<?php
class Kwc_Basic_LinkTag_News_Admin extends Kwc_Basic_LinkTag_Abstract_Admin
    implements Kwf_Component_Abstract_Admin_Interface_DependsOnRow
{
    protected $_prefix = 'news';
    protected $_prefixPlural = 'news';

    public function componentToString(Kwf_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $field = $this->_prefix . '_id';
        $data = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_prefixPlural . '_'.$row->$field, array('subroot' => $data));
        if (!$data) return '';
        return $data->name;
    }

    public function getComponentsDependingOnRow(Kwf_Model_Row_Interface $row)
    {
        // nur bei newsmodel
        if ($row->getModel() instanceof Kwc_News_Directory_Model) {
            $linkModel = Kwf_Model_Abstract::getInstance(
                Kwc_Abstract::getSetting($this->_class, 'ownModel')
            );
            $linkingRows = $linkModel->getRows($linkModel->select()
                ->whereEquals($this->_prefix . '_id', $row->{$row->getModel()->getPrimaryKey()})
            );
            if (count($linkingRows)) {
                $ret = array();
                foreach ($linkingRows as $linkingRow) {
                    $c = Kwf_Component_Data_Root::getInstance()
                        ->getComponentByDbId($linkingRow->component_id);
                    //$c kann null sein wenn es nicht online ist
                    if ($c) $ret[] = $c;
                }
                return $ret;
            }
        }
        return array();
    }

    public function getCardForms()
    {
        $ret = array();
        $news = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByClass('Kwc_News_Directory_Component');
        foreach ($news as $new) {
            if (is_instance_of($new->componentClass, 'Kwc_Events_Directory_Component')) continue;
            $form = Kwc_Abstract_Form::createComponentForm($this->_class, 'child');
            $form->fields['news_id']->setBaseParams(array('newsComponentId'=>$new->dbId));
            $form->fields['news_id']->setFieldLabel($new->getPage()->name);
            $ret[$new->dbId] = array(
                'form' => $form,
                'title' => count($news) > 1 ? $new->getTitle() : trlKwf('News')
            );
        }
        return $ret;
    }

}
