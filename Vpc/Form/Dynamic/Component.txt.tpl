<?=trlVps('You got a requst');?>.

<?
foreach(unserialize($this->field_labels) as $k=>$i) {
    echo "$i:\n".$this->$k."\n\n";
}
