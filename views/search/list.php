<?php include_once('../views/includes/header.php'); ?>
<div data-role="content">
  <ul data-role="listview" data-inset="true" data-divider-theme="a">
    <li data-role='list-divider' role='heading'>Songs</li>
    <?php
      if (count($songs) == 0) {
        echo "<li>No songs found.</li>";
      }
      foreach ($songs as $song) {
        echo "<li>";
        echo "<a href='/songs/{$song->url}'>{$song->title}</a>";
        echo "</li>";
      }
    ?>
  </ul>

  <ul data-role="listview" data-inset="true" data-divider-theme="a">
    <li data-role='list-divider' role='heading'>Groups</li>
    <?php
      if (count($groups) == 0) {
        echo "<li>No groups found.</li>";
      }
      foreach ($groups as $group) {
        echo "<li>";
        echo "<a href='/groups/{$group->url}'>{$group->name}</a>";
        echo "</li>";
      }
    ?>
  </ul>

  <ul data-role="listview" data-inset="true" data-divider-theme="a">
    <li data-role='list-divider' role='heading'>Setlists</li>
    <?php
      if (count($setlists) == 0) {
        echo "<li>No setlists found.</li>";
      }
      foreach ($setlists as $setlist) {
        echo "<li>";
        if ($setlist->group_id) {
          echo "<a href='/groups/{$setlist->group_url}/{$setlist->url}'>{$setlist->title}</a>";
        } else {
          echo "<a href='/personal/{$setlist->url}'>{$setlist->title}</a>";
        }
        echo "</li>";
      }
    ?>
  </ul>

  <ul data-role="listview" data-inset="true" data-divider-theme="a">
    <li data-role='list-divider' role='heading'>Users</li>
    <?php
      if (count($users) == 0) {
        echo "<li>No users found.</li>";
      }
      foreach ($users as $user) {
        echo "<li>";
        echo "{$user->first_name} {$user->last_name}";
        echo "</li>";
      }
    ?>
  </ul>
</div>

<?php include_once('../views/includes/footer.php'); ?>