<div class="<?=$this->cssClass?>">
    <? if (isset($this->searchForm)) echo $this->component($this->searchForm); ?>
    <ul>
        <?= $this->partials($this->data) ?>
    </ul>
</div>