<div class="<?=$this->cssClass?>">
    <? foreach ($this->languages as $l) { ?>
        <?
        if ($l['flag']) {
            $text = $this->component($l['flag']);
        } else {
            $text = $l['language'];
        }
        echo $this->componentLink($l['home'], $text)
        ?>
    <? } ?>
</div>