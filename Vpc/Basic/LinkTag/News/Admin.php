<?php
class Vpc_Basic_LinkTag_News_Admin extends Vpc_Basic_LinkTag_Abstract_Admin
    implements Vps_Component_Abstract_Admin_Interface_DependsOnRow
{
    protected $_prefix = 'news';
    protected $_prefixPlural = 'news';

    public function componentToString(Vps_Component_Data $data)
    {
        $row = $data->getComponent()->getRow();
        $field = $this->_prefix . '_id';
        $data = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_prefixPlural . '_'.$row->$field, array('subroot' => $data));
        if (!$data) return '';
        return $data->name;
    }

    public function getComponentsDependingOnRow(Vps_Model_Row_Interface $row)
    {
        // nur bei newsmodel
        if ($row->getModel() instanceof Vpc_News_Directory_Model) {
            $linkModel = Vps_Model_Abstract::getInstance(
                Vpc_Abstract::getSetting($this->_class, 'ownModel')
            );
            $linkingRows = $linkModel->getRows($linkModel->select()
                ->whereEquals($this->_prefix . '_id', $row->{$row->getModel()->getPrimaryKey()})
            );
            if (count($linkingRows)) {
                $ret = array();
                foreach ($linkingRows as $linkingRow) {
                    $c = Vps_Component_Data_Root::getInstance()
                        ->getComponentByDbId($linkingRow->component_id);
                    //$c kann null sein wenn es nicht online ist
                    if ($c) $ret[] = $c;
                }
                return $ret;
            }
        }
        return array();
    }

    public function getLinkTagForms()
    {
        $ret = array();
        $news = Vps_Component_Data_Root::getInstance()
            ->getComponentsByClass('Vpc_News_Directory_Component');
        foreach ($news as $new) {
            if (is_instance_of($new->componentClass, 'Vpc_Events_Directory_Component')) continue;
            $form = Vpc_Abstract_Form::createComponentForm($this->_class, 'link');
            $form->fields['news_id']->setBaseParams(array('newsComponentId'=>$new->dbId));
            $form->fields['news_id']->setFieldLabel($new->getPage()->name);
            $ret[$new->dbId] = array(
                'form' => $form,
                'title' => $new->getTitle()
            );
        }
        return $ret;
    }

}
