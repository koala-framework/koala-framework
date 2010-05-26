<div class="<?=$this->cssClass?>">
    <h2><?=$this->placeholder['headline']?></h2>
    <ul>
    <? foreach($this->related as $c) { ?>
        <li>
            <h3><?=$this->componentLink($c)?></h3>
            <?=$this->dateTime($c->row->publish_date)?>
            <p><?=$this->truncate($c->row->teaser)?></p>
        </li>
    <? } ?>
    </ul>
</div>
