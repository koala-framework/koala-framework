<div class="<?=$this->cssClass?>">
    <?php foreach ($this->paragraphs as $paragraph) { ?>
        <?php echo $this->component($paragraph) ?>
    <?php } ?>
</div>
