<div class="<?=$this->cssClass?>">

    <div class="nickname"><h3><?=htmlspecialchars($this->row->nickname)?></h3></div>
    
    <?php
    if ($this->rating) {
        echo $this->component($this->rating);
    }
    if ($this->avatar) {
    ?>
    <div class="avatar">
        <?php
        echo $this->component($this->avatar);
        ?>
    </div>
    <?php
    }
    ?>
    <div class="userData">
        <h3><?=trlVps('Member since')?>:</h3>
        <p><?=$this->date($this->row->created)?></p>

        <? if ($this->row->last_login) { ?>
        <h3><?=trlVps('Latest online')?>:</h3>
        <p><?=$this->timestamp($this->row->last_login)?></p>
        <? } ?>
    
        <? if ($this->row->location != '') { ?>
        <h3><?=trlVps('Place of living')?>:</h3>
        <p><?=htmlspecialchars($this->row->location)?></p>
        <? } ?>
    
        <? if ($this->row->description_short != '') { ?>
        <h3><?=trlVps('Short description')?>:</h3>
        <p><?=nl2br(htmlspecialchars($this->row->description_short))?></p>
        <? } ?>
    
        <? if ($this->showLastPosts) { ?>
        <h3><?=trlVps('Last Posts')?>:</h3>
        <ul>
            <?php foreach ($this->lastPosts as $lastPost) { ?>
                <li><p><?= $this->componentLink($lastPost, $lastPost->linktext) ?></p></li>
            <?php } ?>
        </ul>
        <?php } ?>
    </div>
</div>