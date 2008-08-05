<div class="<?=$this->cssClass?>">
    <?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?>
    <ul>
    <? foreach ($this->threads as $thread) { ?>
        <li><?=$this->componentLink($thread)?></li>
    <? } ?>
    </ul>
    <?=$this->componentLink($this->newThread, trlVps('Create a new topic'))?>
</div>