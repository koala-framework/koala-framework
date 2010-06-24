<?php
class Vps_Component_View_Mail extends Vps_View_Mail
{
    public function addImage(Zend_Mime_Part $image)
    {
        $data = $this->data;
        while ($data && !$data->getComponent() instanceof Vpc_Mail_Component) {
            $data = $data->parent;
        }

        if ($data) {
            $data->getComponent()->addImage($image);
        }
    }
}
