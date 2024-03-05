<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component;

use Kwf_Events_Event_Model_Serialization_ColumnChanged;

class UrlEvents extends \Kwf_Events_Subscriber
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Page_UrlChanged',
            'callback' => 'onUrlChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onRecursiveUrlChanged'
        );
        return $ret;
    }

    public function onUrlChanged(\Kwf_Component_Event_Page_UrlChanged $ev)
    {
        $this->_deleteCache($ev->component);
    }

    public function onRecursiveUrlChanged(\Kwf_Component_Event_Page_RecursiveUrlChanged $ev)
    {
        if ($ev->component->componentClass == $this->_config['componentClass']) {
            $this->_deleteCache($ev->component);
        } else {
            foreach ($ev->component->getRecursiveChildComponents(array('componentClass' => $this->_config['componentClass'])) as $component) {
                $this->_deleteCache($component);
            }
        }
    }

    private function _deleteCache(\Kwf_Component_Data $data)
    {
        $model = \Kwf_Model_Factory_Abstract::getModelInstance($this->_config['modelFactoryConfig']);

        $row = null;
        while ($data) {
            if (isset($data->row) && $data->row->getModel() == $model) {
                $row = $data->row;
                break;
            }
            $data = $data->parent;
        }
        if (!$row) {
            throw new \Kwf_Exception("Can't find row matching model for $data->componentId");
        }
        $cacheId =  'normalizer__'.$model->getUniqueIdentifier().'__'.$this->_config['column'].'__'.$row->id;
        \Kwf_Cache_Simple::delete($cacheId);
        self::fireEvent(new Kwf_Events_Event_Model_Serialization_ColumnChanged($row, $this->_config['column']));
    }
}

