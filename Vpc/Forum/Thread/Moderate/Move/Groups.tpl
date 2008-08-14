<ul>
<?php foreach ($this->groups as $g) { ?>
    <li class="<?= $g->row->post == 1 ? 'post' : 'title' ?>">
        <?php if ($g->row->post) { ?>
            <div class="description">
                <?= $this->componentLink($this->data, $g->getPage()->name, null, array('to' => $g->row->id))?>
            </div>
        <?php } else { ?>
            <?= $g->name ?>
        <?php } ?>
        <?php if (!empty($g->childGroups)) {
            echo $this->partial($this->groupsTemplate, array('data' => $this->data, 
                'groups' => $g->childGroups, 'groupsTemplate' => $this->groupsTemplate));
        }
        ?>
    </li>
<?php } ?>
</ul>
