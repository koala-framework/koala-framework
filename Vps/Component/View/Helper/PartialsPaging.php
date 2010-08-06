<?php
class Vps_Component_View_Helper_PartialsPaging extends Vps_Component_View_Helper_Partials
{
    public function partialsPaging($component)
    {
        return $this->partials($component, 'Vps_Component_Partial_Paging');
    }
}
