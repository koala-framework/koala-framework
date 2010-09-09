<div class="<?=$this->cssClass?>">
    <form action="<?=$this->data->url?>">
        <input name="query" />
        <input type="submit" />
    </form>
    <ul>
    <? foreach ($this->hits as $hit) { ?>
        <li><?=$this->componentLink($hit['data'])?></li>
    <? } ?>
    </ul>
</div>