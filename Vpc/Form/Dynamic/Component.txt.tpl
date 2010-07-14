<?=trlVps('You got a requst');?>.

<?
foreach($this->vars->toArray() as $k=>$i) {
    echo "$k: $i\n";
}
