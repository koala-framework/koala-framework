<div class="<?=$this->cssClass?>">
    <? $i = 0;
    foreach ($this->languages as $l) {
        if ($i >= 1) echo $this->separator;

        if ($l['flag']) {
            $text = $this->component($l['flag']);
        } else {
            $text = $l['name'];
        }

        echo $this->componentLink($l['home'], $text);

        $i++;
    } ?>
</div>