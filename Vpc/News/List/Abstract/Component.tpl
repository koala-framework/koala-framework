<?=$this->component($this->paging)?>
<ul>
<?php foreach ($this->news as $n) { ?>
    <li>
    <?=$this->componentLink($n);?>
    <span class="publishDate"><?=$n['publish_date']?></span>
    <p><?=$n['teaser']?></p>
    </li>
<?php } ?>
</ul>
<?=$this->component($this->paging)?>
