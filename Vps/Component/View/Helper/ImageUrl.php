<?php
class Vps_Component_View_Helper_ImageUrl extends Vps_Component_View_Helper_ImageParam
{
    public function imageUrl($image, $type = 'default')
    {
        return $this->imageParam($image, 'url', $type);
    }
}
