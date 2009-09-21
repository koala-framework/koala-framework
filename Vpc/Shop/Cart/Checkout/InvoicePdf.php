<?php
class Vpc_Shop_Cart_Checkout_InvoicePdf extends Vps_Pdf_TcPdf
{
    public function __construct($order)
    {
        parent::__construct();
        $this->SetMargins(25, 15, 25);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetAutoPageBreak(true);

        $this->AddPage();
        $this->setFontSize(10);
        $this->Cell(40, 3, "$order->firstname $order->lastname");
        foreach ($order->getChildRows('Products') as $row) {
            $productPrice = $row->getParentRow('ProductPrice');
            $product = $productPrice->getParentRow('Product');
            $this->Cell(40, 3, "$product $row->amount $row->size $row->price");
        }
    }
}
