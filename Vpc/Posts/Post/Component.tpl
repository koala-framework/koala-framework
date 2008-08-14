<div class="<?=$this->cssClass?>">
    <div class="postData">
        <? if ($this->user) {
            $userText = $this->user->name;
            $rating = $this->user->getChildComponent('-rating');
            if ($rating) $userText .= ' ' . $this->component($rating);
        ?>
        <div class="postInfo">
            <?php
                $image = $this->user->getChildComponent('-avatar')->getComponent()->getImageUrl('forum');
                if (!$image) { $image = '/assets/images/logoLight.jpg'; }
            ?>
            <div class="avatar">
                <?= $this->componentLink($this->user, '<img src="' . $image . '" alt="Avatar" />') ?>
            </div>
            <div class="user">
                <?=trlVps('By')?>: <?= $this->componentLink($this->user) ?>
            </div>
            <? } ?>
            <strong>#<?= $this->postNumber ?></strong>
            <i><?=trlVps('on') ?> <?=$this->date($this->data->row->create_time)?> <?=trlVps('at') ?> <?=$this->time($this->data->row->create_time)?></i><br />
            Beitrag: 
            <? if ($this->edit) { ?>
                <br /><?=$this->componentLink($this->edit)?>
            <? } ?>
            <? if ($this->delete) { ?>
                <br /><?=$this->componentLink($this->delete)?>
            <? } ?>
            <?=$this->componentLink($this->report)?> | <?=$this->componentLink($this->quote)?>
        </div>
        <div class="comment">
            <?=$this->content?>
        </div>
        <? if ($this->signature) { ?>
            <p class="signature"><tt>--<br /><?=$this->signature?></tt></p>
        <?php } ?>
    </div>
    <div class="clear"></div>
</div>