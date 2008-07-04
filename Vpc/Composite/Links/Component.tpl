<ul class="<?=$this->cssClass?>">
    <?php foreach ($this->children as $child) { ?>
        <li><?php echo $this->component($child) ?></li>
    <?php } ?>
</ul>
