<div class="vpcForumUser">
    <h1>{trlVps text="Userprofile:"}</h1>
    <div class="text">

        {if $component.forumUserData.nickname}
            <p><strong>Name für Forum:</strong> {$component.forumUserData.nickname}</p>
        {/if}

        {if $component.userData.firstname || $component.userData.lastname}
            <p><strong>{trlVps text="Name"}:</strong>
            {$component.userData.title}
            {$component.userData.firstname}
            {$component.userData.lastname}</p>
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
                <strong>{trlVps text="Signature"}:</strong>
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
                            <a href="{$thread.url}">{$thread.subject}</a>
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
                            <a href="{$post.url}">{$post.subject}</a>
                        </li>
                    {/foreach}
                </ul>
            </p>
        {/if}
    </div>
</div>