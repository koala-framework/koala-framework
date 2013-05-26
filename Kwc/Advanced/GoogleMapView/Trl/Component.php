<?php
class Kwc_Advanced_GoogleMapView_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public function hasContent()
    {
        return $this->getData()->chained->hasContent();
    }
}
