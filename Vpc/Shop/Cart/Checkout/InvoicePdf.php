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
        
        $this->Image(dirname(__FILE__).'/babytuchLogoPrint.jpg', 110, 10, 80, 23, "JPG");
        $this->Line(10, 105, 11, 105);

        $this->setY(44);
        $this->SetFont("Arial", "", 9);
        $this->writeHTMLCell (160, 0, 20, 0, "<b>Babytuch</b>  .  Schnepfenstraße 4  .  A-5302 Henndorf", 0, 'L');
        $prePos = $this->GetY();
        
        $this->setY($prePos+14);
        $this->SetFont("Arial", "", 12);
        if($order->title) { $order->title .= " "; }
        $this->MultiCell(110, 0, $order->title.$order->firstname." ".$order->lastname, 0, 'L');
        $this->MultiCell(110, 0, $order->street, 0, 'L');
        $prePos = $this->GetY();
        $this->setY($prePos-2);
        $this->SetFont("Arial", "B", 12);
        $this->MultiCell(110, 0, trim($order->zip)." ".$order->city, 0, 'L');
        $prePos = $this->GetY();
        if ($order->country!='Österreich') {
            $this->setY($prePos+6);
            $this->SetFont("Arial", "", 12);
            $this->MultiCell(110, 0, $order->country, 0, 'L');
            $prePos = $this->GetY();
        }
        
        $this->setY(44);
        $this->setX(137);
        $this->SetFont("Arial", "", 9);
        $this->MultiCell(100, 0, "www.babytuch.com\nkundenservice@babytuch.com\n".
        "\nBestellnummer:\n$order->order_number\n".
        "\nKundennummer:\n$order->invoice_number\n\nRechnungsnummer:\n$order->customer_number\n".
        "\nRechnungsdatum:\n".$dateHelper->date($order->invoice_date), 0, 'L');

        $this->setY(110);
        $this->SetFont("Arial", "", 18);
        $this->MultiCell(120, 15, "Ihre Rechnung", 0, 'L');
        $this->SetFont("Arial", "", 12);
        $this->SetLineStyle(array('width' => 0.3, '', '', 'dash' => 2, 'color' => array(170,0,0)));
        $prePos = $this->GetY();
        foreach ($order->getChildRows('Products') as $row) {
            $productPrice = $row->getParentRow('ProductPrice');
            $product = $productPrice->getParentRow('Product');
            $this->MultiCell(120, 0, $row->amount."x Babytuch ".$product.", Größe ".$row->size, 0, 'L');
            
            $this->setY($prePos);
            $this->setX(155);
            $this->MultiCell(35, 0, $moneyHelper->money($row->price*$row->amount), 0, 'R');
            $this->Line(20, $prePos+7, 190, $prePos+7);
            $prePos = $this->GetY();
        }

        $this->setY($prePos+8);
        $checkout = Vps_Component_Data_Root::getInstance()->getComponentByDbId($order->checkout_component_id);
        foreach ($checkout->getComponent()->getSumRows($order) as $addSumRow) {
            if(isset($addSumRow['class']) && $addSumRow['class']=='subtotal'){
                $this->SetFont("Arial", "I", 12);
            } else if(isset($addSumRow['class']) && $addSumRow['class']=='totalAmount'){
                $this->SetFont("Arial", "B", 12);
                $this->SetTextColor(170,0,0);
            } else {
                $this->SetFont("Arial", "", 12);
            }
            $this->setY($prePos);
            $this->MultiCell(130, 0, $addSumRow['text'], 0, 'R');
            $this->setY($prePos);
            $this->setX(155);
            $this->MultiCell(35, 0, $moneyHelper->money($addSumRow['amount']), 0, 'R');
            if(isset($addSumRow['class']) && $addSumRow['class']=='totalAmount'){
                $this->SetLineStyle(array('width' => 0.6, '', '', 'dash' => 0, 'color' => array(170,0,0)));
                $this->Line(119, $prePos+7, 190, $prePos+7);
                $this->SetLineStyle(array('width' => 0.3, '', '', 'dash' => 0, 'color' => array(170,0,0)));
                $this->Line(119, $prePos+8, 190, $prePos+8);
            }
            $prePos = $this->GetY();
        }
        
        $this->SetLineStyle(array('width' => 0.2, '', '', 'dash' => 0, 'color' => array(0)));
        
        $this->setY($prePos+20);
        $this->SetFont("Arial", "", 12);
        $this->SetTextColor(0);
        if($order->payment=='cashOnDelivery') {
            $this->MultiCell(160, 0, "CASH-ON-DELIVERY Versandartspezifischer Text", 0, 'L');
        } else if($order->payment=='prePayment') {
            $this->MultiCell(160, 0, "PREPAYMENT Versandartspezifischer Text", 0, 'L');
        } else if($order->payment=='payPal') {
            $this->MultiCell(160, 0, "PAYPAL Versandartspezifischer Text", 0, 'L');
        }
        $this->MultiCell(160, 0, "Wir danken für Ihr Vertrauen!", 0, 'L');

        $prePos = "-20";
        
        $this->setY($prePos);
        $this->SetFont("Arial", "", 7);
        $this->writeHTMLCell (160, 0, 20, 0, "<b>Babytuch</b><br>Schnepfenstraße 4<br>A-5302 Henndorf", 0, 'L');
        
        $this->Line(51, 279, 51, 286);
        
        $this->setY($prePos);
        $this->writeHTMLCell (160, 0, 60, 0, "Tel. +43(0)699-11088099<br>E-Mail: kontakt@babytuch.com<br>Web: www.babytuch.com", 0, 'L');

        $this->Line(103, 279, 103, 286);
        
        $this->setY($prePos);
        $this->writeHTMLCell (160, 0, 110, 0, "Bankverbindung:<br>Raiffeisenbank Henndorf<br>BLZ 35024, Kto-Nr. 53710", 0, 'L');

        $this->Line(146, 279, 146, 286);
        
        $this->setY($prePos);
        $this->writeHTMLCell (160, 0, 152, 0, "International:<br>IBAN AT2335024000000053710<br>BIC RVSAAT2S024", 0, 'L');
    }
}
