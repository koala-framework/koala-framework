<ul>
<? $i = 0;
foreach ($this->related as $r) { ?>
    <li class="<? if ($i++ == 0) echo 'first'; ?>">
        <?= $this->componentLink($r,
            $this->truncate($r->row->name1.', '.$r->row->zipcode.' '.$r->row->city, 60, '...', true)
        ); ?>
    </li>
<? } ?>
</ul>