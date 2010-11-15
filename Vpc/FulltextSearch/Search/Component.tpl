<div class="<?=$this->cssClass;?>">
    <form action="<?=$this->data->url;?>">
        <input name="query" value="<?=htmlspecialchars($this->queryString);?>" />
        <input type="submit" value="<?=$this->data->trlVps('Search');?>" />
    </form>
    <? if ($this->error) { ?>
        <p><?=$this->error?></p>
    <? } else if ($this->hits) { ?>
        <div class="resultText">
            <?=$this->data->trlVps('<strong>Found Results</strong> (in {0} seconds)', round($this->queryTime, 2));?>
            <?=$this->data->trlVps('{0}-{1} of about {2}', array($this->numStart, $this->numEnd, $this->hitCount));?>
        </div>
        <?=$this->component($this->paging);?>
        <ul class="resultList">
            <? foreach ($this->hits as $hit) { ?>
                <li>
                    <?=$this->componentLink($hit['data'],$this->highlightTerms($this->queryParts, $hit['data']->name));?>
                    <span class="preview"><?=$this->highlightTerms($this->queryParts, $hit['content']);?></span>
                </li>
            <? } ?>
        </ul>
        <?=$this->component($this->paging)?>
    <? } else if ($this->queryString) { ?>
        <div class="resultText">
            <?=$this->data->trlVps('No results found for <strong>"{0}"</strong>',htmlspecialchars($this->queryString));?>
        </div>
    <? } ?>

    <p><?=$this->placeholder['helpFooter']?></p>
</div>