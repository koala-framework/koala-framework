<div class="<?=$this->rootElementClass?>">
    <ul>
        <?php foreach ($this->childPages as $cp) { ?>
            <li><?= $this->componentLink($cp); ?></li>
        <?php } ?>
    </ul>
</div>
