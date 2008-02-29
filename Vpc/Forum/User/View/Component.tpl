<div class="vpcForumUser">
    <h1>User:</h1>
    <div class="text">
        <p><strong>Name:</strong> {$component.userData.title} {$component.userData.firstname} {$component.userData.lastname}</p>
        <p><strong>Mitglied seit:</strong> {$component.userData.created|date_format:"%d.%m.%y, %H:%M"}</p>
        <p><strong>Zuletzt online:</strong> {$component.userData.last_login|date_format:"%d.%m.%y, %H:%M"}</p>
        <p><strong>Kurzbeschreibung:</strong> {$component.forumUserData.description_short|nl2br}</p>
        <p><strong>Threads:</strong> {$component.userThreads}</p>
        <p><strong>Posts:</strong> {$component.userPosts}</p>
    </div>
</div>