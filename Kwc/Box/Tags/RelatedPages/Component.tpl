<div class="<?=$this->rootElementClass?>">
    <?php if ($this->placeholder['headline']) { ?>
    <h1><?=$this->placeholder['headline']?></h1>
    <?php } ?>
    <ul>
    <?php foreach ($this->related as $c) { ?>
        <li><?=$this->componentLink($c)?></li>
    <?php } ?>
    </ul>
</div>
