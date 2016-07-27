<div class="<?=$this->rootElementClass?>">
    <?php foreach($this->keys as $k) { ?>
        <?=$this->component($this->$k)?>
    <?php } ?>
</div>
