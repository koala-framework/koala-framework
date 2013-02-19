<?php
class Kwc_Shop_Cart_Checkout_InvoicePdf extends Kwf_Pdf_TcPdf
{
    public function __construct($order)
    {
        parent::__construct();
        $moneyHelper = new Kwf_View_Helper_Money();
        $dateHelper = new Kwf_View_Helper_Date();
        
        $data = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($orderOrReplacement->checkout_component_id);


        $this->SetMargins(20, 15, 20);
        $this->setPrintHeader(false);
        $this->setPrintFooter(false);
        $this->SetAutoPageBreak(true);
        
        $this->AddPage();
        
        $this->SetFont("Arial", "", 9);
        
        if($order->title) { $order->title .= " "; }
        $this->MultiCell(0, 0, $order->title.$order->firstname." ".$order->lastname, 0, 'L');
        
        $this->MultiCell(0, 0, "\n".$data->trl('Order Number').":\n$order->order_number\n".
        "\n".$data->trl('Customer Number').":\n$order->customer_number\n".
        "\n".$data->trl('Invoice Number').":\n$order->invoice_number\n".
        "\n".$data->trl('Invoice Date').":\n".$dateHelper->date($order->invoice_date), 0, 'L');

        foreach ($order->getProductsData() as $item) {
            $text = $item->amount."x ".$item->text;
            foreach ($item->additionalOrderData as $d) {
                if ($d['class'] != 'amount') {
                    $text .= ", $d[name] $d[value]";
                }
            }
            $this->MultiCell(120, 0, $data->trlStaticExecute($text), 0, 'L');
            $this->MultiCell(35, 0, $moneyHelper->money($item->price), 0, 'R');
        }

        $orderData = Kwc_Shop_Cart_OrderData::getInstance($order->cart_component_class);
        foreach ($orderData->getSumRows($order) as $addSumRow) {
            if(isset($addSumRow['class']) && $addSumRow['class']=='totalAmount'){
                $this->MultiCell(0, 0, $data->trlStaticExecute($addSumRow['text'])." ".$moneyHelper->money($addSumRow['amount']), 0, 'L');
            }
        }
    }
}
