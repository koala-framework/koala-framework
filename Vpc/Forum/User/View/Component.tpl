<div class="vpcForumUser">
    <h1>{trlVps text="Userprofile:"}</h1>
    <div class="text">

        {if $component.forumUserData.avatarUrl}
            <div class="avatar"><img src="{$component.forumUserData.avatarUrl}" alt="Avatar" /></div>
        {/if}

        {if $component.forumUserData.nickname}
            <p><strong>Name für Forum:</strong> {$component.forumUserData.nickname}</p>
        {/if}

        {if $component.userData.firstname || $component.userData.lastname}
            <p><strong>{trlVps text="Name"}:</strong>
            {$component.userData.title}
            {$component.userData.firstname}
            {$component.userData.lastname|truncate:2:'.':true}</p>
        {/if}

        <p>
            <strong>{trlVps text="Member since"}:</strong>
            {$component.userData.created|date_format:"%d.%m.%y"}
        </p>

        <p>
            <strong>{trlVps text="Latest online"}:</strong>
            {$component.userData.last_login|date_format:"%d.%m.%y, %H:%M"}
        </p>

        {if $component.forumUserData.location}
            <p><strong>{trlVps text="Town"}:</strong> {$component.forumUserData.location}</p>
        {/if}

        {if $component.forumUserData.description_short}
            <p>
                <strong>{trlVps text="Short description"}:</strong>
                {$component.forumUserData.description_short|htmlspecialchars|nl2br}
            </p>
        {/if}

        {if $component.forumUserData.signature}
            <p>
                <strong>{trlcVps context="forum" text="Signature"}:</strong>
                {$component.forumUserData.signature|htmlspecialchars|nl2br}
            </p>
        {/if}

        {if $component.lastThreads}
            <p>
                <strong>{trlVps text="Latest topics"} ({$component.userThreads} gesamt):</strong>
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
                <strong>{trlVps text="Latest entries"} ({$component.userPosts} gesamt):</strong>
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