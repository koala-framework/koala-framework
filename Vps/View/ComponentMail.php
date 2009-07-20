<?php
class Vps_View_ComponentMail extends Vps_View_Component
    implements Vps_View_MailInterface
{
    // Diese View kann es nur in einer Unterkomponente von Vpc_Mail geben,
    // diese suchen und das Image hinzufügen
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
