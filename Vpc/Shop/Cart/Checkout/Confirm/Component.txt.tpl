Guten Tag, <?=$this->order->getSalutation()?>!

Sie haben auf www.babytuch.com folgende Artikel bestellt:
<? foreach ($this->products as $p) { ?>
<?=$p->amount?> Babytuch, <?=$p->getParentRow('Product')?>, Größe <?=$p->size?>: EUR <?=$this->money($p->getTotal())?>
<? } ?>

Versandkosten: EUR <?=$this->money($this->order->getShipping())?>

          ---------------
            EUR <?=$this->money($this->order->getTotal())?>


Bitte überweisen Sie EUR <?=$this->money($this->order->getTotal())?> auf folgendes Konto:

Kontoinhaber: Babytuch
Bankleitzahl: 35024
Kontonummer: 53710
Bitte geben Sie unbedingt Ihre Bestellnummer an:  "<?=$this->order->getOrderNumber()?>"

Für internationale Zahlungen:
IBAN: AT23 3502 4000 0005 3710
BIC: RVSAAT2S024

Nach Eingang der Zahlung wird Ihre Bestellung verschickt.

Vielen Dank für Ihren Einkauf!
Herzlichst
Ihr Babytuch-Team
