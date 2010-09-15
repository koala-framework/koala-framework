<div class="<?=$this->cssClass?>">
    <form action="<?=$this->data->url?>">
        <input name="query" value="<?=htmlspecialchars($this->queryString)?>" />
        <input type="submit" value="<?=$this->data->trlVps('Search')?>" />
    </form>
    <? if ($this->hits) { ?>

    <?=$this->data->trlVps('<strong>Found Results</strong> (in {0} seconds)', round($this->queryTime, 2))?>
    <?=$this->data->trl('{0}-{1} of about {2}', array($this->numStart, $this->numEnd, $this->hitCount))?>
    <?=$this->component($this->paging)?>

    <ul>
    <? foreach ($this->hits as $hit) { ?>
        <li>
            <?=$this->componentLink($hit['data'])?>
            <span class="preview"><?=$this->highlightTerms($this->queryParts, $hit['content'])?></span>
        </li>
    <? } ?>
    </ul>
    <? } ?>

    <?=$this->component($this->paging)?>
</div>