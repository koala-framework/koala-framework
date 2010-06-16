<?php
class Vpc_Basic_ImageEnlarge_EnlargeTag_Trl_Image_Component
    extends Vpc_Abstract_Image_Trl_Image_Component
{
    public function getImageData()
    {
        $d = $this->getData();
        while (!is_instance_of($d->componentClass, 'Vpc_Basic_ImageEnlarge_Trl_Component')) {
            $d = $d->parent;
        }

        if ($d->getComponent()->getRow()->own_image) {
            return $d->getChildComponent('-image')->getComponent()->getOwnImageData();
        }

        return $this->getData()->parent->chained->getComponent()->getImageData();
    }

    public function getOwnImageData()
    {
        return parent::getImageData();
    }
}
