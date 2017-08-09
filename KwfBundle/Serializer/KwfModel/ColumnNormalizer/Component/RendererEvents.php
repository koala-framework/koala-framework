<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component;
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
        $data = $ev->data;
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
    }
}

