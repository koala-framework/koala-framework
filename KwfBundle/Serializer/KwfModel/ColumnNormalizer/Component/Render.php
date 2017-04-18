<?php
namespace KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component;

use Kwf_Model_Interface;
use Kwf_Model_Row_Interface;
use Kwf_Exception;
use Kwf_Component_Data_Root;
use Kwf_Model_EventSubscriber;
use KwfBundle\Serializer\KwfModel\ColumnNormalizer\ColumnNormalizerInterface;
use KwfBundle\Serializer\KwfModel\ColumnNormalizer\CacheableInterface;

class Render implements ColumnNormalizerInterface, CacheableInterface
{
    public function normalize(Kwf_Model_Row_Interface $row, $column, array $settings, $format = null, array $context = array())
    {
        $c = null;
        if (isset($settings['idTemplate'])) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentById(str_replace('{0}', $row->id, $settings['idTemplate']));
        } else if (isset($settings['dbIdTemplate'])) {
            $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(str_replace('{0}', $row->id, $settings['dbIdTemplate']), array('limit'=>1));
        } else {
            throw new Kwf_Exception("idTempalte or dbIdTemplate is required");
        }
        if (!$c) {
            $e = new Kwf_Exception("Component not found");
            $e->logOrThrow();
            return '';
        }
        if ($c->hasContent()) {
            $ret = $c->render();
        } else {
            $ret = '';
        }
        return $ret;
    }

    public function getEventSubscribers(Kwf_Model_Interface $model, $column, array $settings)
    {
        return array(
            Kwf_Model_EventSubscriber::getInstance('KwfBundle\Serializer\KwfModel\ColumnNormalizer\Component\RendererEvents', array(
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
