<ul class="<?=$this->cssClass?>">
    <? foreach ($this->lists as $list) { ?>
        <li>
            <?= $list['list']->name; ?>
            <ul>
                <? foreach ($list['items'] as $item) { ?>
                    <li>
                        <?= $this->componentLink($item); ?>
                    </li>
                <? } ?>
            </ul>
        </li>
    <? } ?>
</ul>
