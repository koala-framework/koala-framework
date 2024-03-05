<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component;

use Kwf_Events_Event_Model_Serialization_ColumnChanged;

class ImageEvents extends \Kwf_Events_Subscriber
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Events_Event_Media_Changed',
            'callback' => 'onImageUrlChanged'
        );
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onImageChanged'
        );
        return $ret;
    }

    public function onImageUrlChanged(\Kwf_Events_Event_Media_Changed $ev)
    {
        $this->clearCacheForComponent($ev->component);
    }

    public function onImageChanged(\Kwf_Component_Event_Component_ContentChanged $ev)
    {
        $this->clearCacheForComponent($ev->component);
    }

    private function clearCacheForComponent(\Kwf_Component_Data $data)
    {
        $model = \Kwf_Model_Factory_Abstract::getModelInstance($this->_config['modelFactoryConfig']);

        $row = null;
        $d = $data;
        while ($d) {
            if (isset($d->row) &&
                $d->row instanceof \Kwf_Model_Row_Interface &&
                $d->row->getModel() == $model
            ) {
                $row = $d->row;
                break;
            }
            $d = $d->parent;
        }
        if (!$row) {
            throw new \Kwf_Exception("Can't find row matching model for $data->componentId");
        }
        $cacheId = 'normalizer__'.$model->getUniqueIdentifier().'__'.$this->_config['column'].'__'.$row->id;
        \Kwf_Cache_Simple::delete($cacheId);
        self::fireEvent(new Kwf_Events_Event_Model_Serialization_ColumnChanged($row, $this->_config['column']));
    }
}

