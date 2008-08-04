<div class="<?=$this->cssClass?>">
    <h1><?=trlVps('Userprofile:')?></h1>
    <div class="text">
        {if $component.forumUserData.avatarUrl}
            <div class="avatar"><img src="{$component.forumUserData.avatarUrl}" alt="Avatar" /></div>
        {/if}

        {if $component.forumUserData.nickname}
          <h3>{$component.forumUserData.nickname}
              {if $component.rating}
                {section name=rating start=0 loop=$component.rating}
                    <img src="/assets/web/images/btnPfotenWeis.jpg" width="10" height="10" alt="" />
                {/section}
              {/if}
            </h3>

        <? if ($this->data->row->firstname || $this->data->row->lastname) { ?>
            <h3>
                <?=$this->data->row->title?>
                <?=$this->data->row->firstname?>
                <?=substr($this->data->row->lastname, 0, 1).'.'?>
                <? if (isset($this->data->row->rating)) { ?>
                    <? for ($i = 0; $i < $this->data->row->rating; $i++) { ?>
                        <img src="/assets/web/images/btnPfotenWeis.jpg" width="10" height="10" alt="" />
                    <? } ?>
                <? } ?>
            </h3>
        <? } ?>

        <p>
            <strong><?=trlVps('Member since')?>:</strong>
            <?=$this->date($this->data->row->created)?>
        </p>

        <p>
            <strong><?=trlVps('Latest online')?>:</strong>
            <?=$this->dateTime($this->data->row->last_login)?>
        </p>

        {if $component.forumUserData.location}
            <p><strong><?=trlVps('Town')?>:</strong> {$component.forumUserData.location}</p>
        {/if}

        {if $component.forumUserData.description_short}
            <p>
                <strong><?=trlVps('Short description')?>:</strong>
                {$component.forumUserData.description_short|htmlspecialchars|nl2br}
            </p>
        {/if}

        {if $component.ownedEntries}
            <p>
                <strong>Verknüpfte Einträge:</strong>
                <ul>
                    {foreach from=$component.ownedEntries item=c}
                        <li><a href="{$c.url}">{$c.data.name1}, {$c.data.zipcode} {$c.data.city}</a></li>
                    {/foreach}
                </ul>
            </p>
        {/if}

        {if $component.forumUserData.signature}
            <p>
                <strong><?=trlcVps('forum', 'Signature')?>:</strong>
                {$component.forumUserData.signature|htmlspecialchars|nl2br}
            </p>
        {/if}

        {if $component.lastThreads}
            <p>
                <strong><?=trlVps('Latest topics')?> ({$component.userThreads} gesamt):</strong>
                <ul>
                    {foreach from=$component.lastThreads item=thread}
                        <li>
                            {$thread.create_time|date_format:"%d.%m.%y"}:
                            <a href="{$thread.url}">{$thread.subject|htmlspecialchars}</a>
                        </li>
                    {/foreach}
                </ul>
            </p>
        {/if}

        {if $component.lastPosts}
            <p>
                <strong><?=trlVps('Latest entries')?> ({$component.userPosts} gesamt):</strong>
                <ul>
                    {foreach from=$component.lastPosts item=post}
                        <li>
                            {$post.create_time|date_format:"%d.%m.%y"}:
                            <a href="{$post.url}">{$post.subject|htmlspecialchars}</a>
                        </li>
                    {/foreach}
                </ul>
            </p>
        {/if}
    </div>


    {component component=$component.images}

    <h1>Gästebuch:</h1>
    {component component=$component.guestbook}
</div>