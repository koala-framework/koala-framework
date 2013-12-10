<?php
class Kwc_Basic_ImageEnlarge_Trl_Component extends Kwc_Abstract_Image_Trl_Component
{
    public function onCacheCallback($row)
    {
        $img = $this->getData()->getChildComponent('-image');
        $cacheId = Kwf_Media::createCacheId(
            $img->componentClass, $img->componentId, 'default'
        );
        Kwf_Media::getOutputCache()->remove($cacheId);
    }

    public function getImageData()
    {
        if ($this->getRow()->own_image) {
            return $this->getData()->getChildComponent('-image')->getComponent()->getImageData();
        }
        return $this->getData()->chained->getComponent()->getImageData();
    }
}
