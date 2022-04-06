<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component;

use Kwf_Model_Interface;
use Kwf_Model_Row_Interface;
use Kwf_Exception;
use Kwf_Component_Data_Root;
use Kwf_Model_EventSubscriber;
use KwfBundle\Serializer\KwfModel\ColumnNormalizer\ColumnNormalizerInterface;
use KwfBundle\Serializer\KwfModel\ColumnNormalizer\CacheableInterface;

class Url implements ColumnNormalizerInterface, CacheableInterface
{
    protected $eventSubscriberClass = 'KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component\UrlEvents';

    public function normalize(Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array())
    {
        $c = $this->getComponent($row, $settings);
        if (!$c || !$c->isPage) {
            $e = new Kwf_Exception("Component not found or not a page");
            $e->logOrThrow();
            return null;
        }
        return $c->getAbsoluteUrl();
    }

    protected function getComponentId($row, $settings)
    {
        if (isset($settings['idTemplate'])) {
            return str_replace('{0}', $row->id, $settings['idTemplate']);
        } else {
            return str_replace('{0}', $row->id, $settings['dbIdTemplate']);
        }
    }

    protected function getComponent($row, $settings)
    {
        if (isset($settings['idTemplate'])) {
            return Kwf_Component_Data_Root::getInstance()->getComponentById($this->getComponentId($row, $settings));
        } else if (isset($settings['dbIdTemplate'])) {
            return Kwf_Component_Data_Root::getInstance()->getComponentByDbId($this->getComponentId($row, $settings), array('limit'=>1));
        } else {
            throw new Kwf_Exception("idTemplate or dbIdTemplate is required");
        }
    }

    public function getEventSubscribers(Kwf_Model_Interface $model, $column, array $settings)
    {
        return array(
            Kwf_Model_EventSubscriber::getInstance($this->eventSubscriberClass, array(
                'modelFactoryConfig' => $model->getFactoryConfig(),
                'componentClass' => $settings['componentClass'],
                'column' => $column
            ))
        );

    }

    public function getCacheId(Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array())
    {
        return 'normalizer__'.$row->getModel()->getUniqueIdentifier().'__'.$column.'__'.$row->id;
    }
}
