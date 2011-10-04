<? if ($this->links) { ?>
    <div class="<?=$this->cssClass?>">
        <? $i = 0;
        foreach($this->links as $l) { ?>
            <?
            $class = '';
            if($i==0) $class .= 'first ';
            if($i==count($this->links)-1) $class .= 'last ';
            ?>
            <?=$this->componentLink($l,$l->name,trim($class))?>
            <? if($i < count($this->links)-1) { ?><?=$this->separator?><? } ?>
        <? $i++;
        } ?>
    </div>
<? } ?>