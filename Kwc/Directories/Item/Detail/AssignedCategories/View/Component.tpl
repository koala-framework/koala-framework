<div class="<?=$this->rootElementClass?>">
    <?php if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <ul>
        <?= $this->partials($this->data) ?>
    </ul>
</div>
