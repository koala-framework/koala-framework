<?php
class Vpc_Shop_Cart_Checkout_InvoicePdf extends Vps_Pdf_TcPdf
{
    public function __construct($order)
    {
        parent::__construct();
        $moneyHelper = new Vps_View_Helper_Money();
        $dateHelper = new Vps_View_Helper_Date();

        $this->SetMargins(20, 15, 20);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetAutoPageBreak(true);
        
        $this->AddPage();
        
        $this->SetFont("Arial", "", 9);
        
        if($order->title) { $order->title .= " "; }
        $this->MultiCell(0, 0, $order->title.$order->firstname." ".$order->lastname, 0, 'L');
        
        $this->MultiCell(0, 0, "\nBestellnummer:\n$order->order_number\n".
        "\nKundennummer:\n$order->invoice_number\n".
        "\nRechnungsnummer:\n$order->customer_number\n".
        "\nRechnungsdatum:\n".$dateHelper->date($order->invoice_date), 0, 'L');
        
        foreach ($order->getChildRows('Products') as $row) {
            $productPrice = $row->getParentRow('ProductPrice');
            $product = $productPrice->getParentRow('Product');
            $this->MultiCell(0, 0, $row->amount."x ".$product.", Größe ".$row->size, 0, 'L');
            $this->MultiCell(0, 0, $moneyHelper->money($row->price*$row->amount), 0, 'L');
        }
        $checkout = Vps_Component_Data_Root::getInstance()->getComponentByDbId($order->checkout_component_id);
        foreach ($checkout->getComponent()->getSumRows($order) as $addSumRow) {
            if(isset($addSumRow['class']) && $addSumRow['class']=='totalAmount'){
                $this->MultiCell(0, 0, $addSumRow['text']." ".$moneyHelper->money($addSumRow['amount']), 0, 'L');
            }
        }
    }
}
