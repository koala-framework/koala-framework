<div class="<?=$this->cssClass?>">

    <div class="nickname"><h3><?=htmlspecialchars($this->row->nickname)?></h3></div>
    
    <?php
    if ($this->avatar) {
    ?>
    <div class="avatar">
        <?php
        echo $this->component($this->avatar);
        ?>
    </div>
    <?php
    }
    if ($this->rating) {
        echo $this->component($this->rating);
    }
    ?>
    <div class="userData">
        <h3><?=trlKwf('Member since')?>:</h3>
        <p><?=$this->date($this->row->created)?></p>

        <? if (isset($this->row->last_login_web)) { ?>
            <h3><?=trlKwf('Latest online')?>:</h3>
            <p><?=$this->timestamp($this->row->last_login_web)?></p>
        <? } ?>

        <? if ($this->row->location != '') { ?>
        <h3><?=trlKwf('Place of living')?>:</h3>
        <p><?=htmlspecialchars($this->row->location)?></p>
        <? } ?>
    
        <? if ($this->row->description_short != '') { ?>
        <h3><?=trlKwf('Short description')?>:</h3>
        <p><?=nl2br(htmlspecialchars($this->row->description_short))?></p>
        <? } ?>
    
        <h3><?=trlKwf('Last Posts')?>:</h3>
        <?= $this->component($this->latestPosts) ?>
    </div>
</div>