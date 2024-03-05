<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component;

use Kwf_Events_Event_Model_Serialization_ColumnChanged;

class RendererEvents extends \Kwf_Events_Subscriber
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onContentChanged'
        );
        return $ret;
    }

    public function onContentChanged(\Kwf_Component_Event_Component_ContentChanged $ev)
    {
        $model = \Kwf_Model_Factory_Abstract::getModelInstance($this->_config['modelFactoryConfig']);

        $row = null;
        $data = $ev->component;
        while ($data) {
            if (isset($data->row) && $data->row->getModel() == $model) {
                $row = $data->row;
                break;
            }
            $data = $data->parent;
        }
        if (!$row) {
            throw new \Kwf_Exception("Can't find row matching model for $ev->data->componentId");
        }
        $cacheId =  'normalizer__'.$model->getUniqueIdentifier().'__'.$this->_config['column'].'__'.$row->id;
        \Kwf_Cache_Simple::delete($cacheId);
        self::fireEvent(new Kwf_Events_Event_Model_Serialization_ColumnChanged($row, $this->_config['column']));
    }
}

