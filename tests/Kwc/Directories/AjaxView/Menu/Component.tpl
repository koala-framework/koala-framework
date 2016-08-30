<div class="<?=$this->rootElementClass?>">
    <?=$this->componentLink($this->directory, null, array('cssClass'=>$this->data->parent===$this->directory ? 'current' : ''))?>

    <ul>
    <?php foreach ($this->categories as $c) { ?>
        <li><?=$this->componentLink($c, null, array('cssClass'=>($this->data->parent===$c ? 'current' : '')))?></li>
    <?php } ?>
    </ul>
</div>
