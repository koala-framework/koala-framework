<li>
    <?=$this->componentLink($this->item);?>
    <span class="rating"><?=$this->component($this->item->getChildComponent('-general')->getChildComponent('-rating'));?></span>
    <span class="memberSince">(<?=trlVps('Member since')?>:
        <?=$this->date($this->item->row->created);?>)</span>
</li>
