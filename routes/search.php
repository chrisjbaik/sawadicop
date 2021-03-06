<?php
$app->group('/search', $acl_middleware(), function () use ($app) {
  $app->get('/', function () use ($app) {
    $req = $app->request();
    $query = $req->params('q');
    $songs = Model::factory('Song')->
      raw_query("SELECT * FROM `song_fts`, `song` WHERE song_fts MATCH :query AND song_fts.rowid = song.id", array('query' => $query))->find_many();
    $like_query = '%' . $query . '%';
    $users = Model::factory('User')->raw_query('SELECT id, first_name, last_name FROM `user` WHERE first_name LIKE :like_query OR last_name LIKE :like_query OR email LIKE :like_query', array('like_query' => $like_query))->find_many();
    $user = $_SESSION['user'];
    $groups = array_column($user->groups()->find_array(), 'id');
    $setlists = $user->setlists()->raw_query('SELECT id, title, url, group_id FROM `setlist` WHERE (user_id = :userid OR group_id IN (:groups)) AND title LIKE :like_query', array('userid' => $user->id, 'groups' => join(',', $groups), 'like_query' => $like_query))->find_many();
    foreach ($setlists as $setlist) {
      if ($setlist->group_id) {
        // Group setlist
        $setlist->group_url = $setlist->group()->find_one()->url;
      }
    }
    $groups = $user->groups()->raw_query('SELECT id, name, url FROM `group` WHERE name LIKE :like_query', array('like_query' => $like_query))->find_many();
    $app->render('search/list.php', array(
      'songs' => $songs,
      'users' => $users,
      'groups' => $groups,
      'setlists' => $setlists,
      'page_title' => 'Search for "' . $query . '"'
    ));
  });

  $app->get('/songs/:query', function ($query) use ($app) {
    $res = $app->response();
    $songs = Model::factory('Song')->raw_query("SELECT * FROM `song_fts` WHERE song_fts MATCH :query", array('query' => $query))->find_array();
    $res->write(json_encode($songs));
  });

  $app->get('/song_titles/:query', function ($query) use ($app) {
    $res = $app->response();
    $query = '%' . $query . '%';
    $songs = Model::factory('Song')->raw_query('SELECT id, title, key, artist FROM `song` WHERE title LIKE :query', array('query' => $query))->find_array();
    $res->write(json_encode($songs));
  });

  $app->get('/users/:query', function ($query) use ($app) {
    $res = $app->response();
    $query = '%' . $query . '%';
    $users = Model::factory('User')->raw_query('SELECT id, first_name, last_name FROM `user` WHERE first_name LIKE :query OR last_name LIKE :query OR email LIKE :query ORDER BY first_name, last_name', array('query' => $query))->find_array();
    $res->write(json_encode($users));
  });

  $app->get('/tags/:query', function ($query) use ($app) {
    $res = $app->response();
    $query = '%' . $query . '%';
    $tags = Model::factory('Tag')->raw_query('SELECT id, name FROM `tag` WHERE name LIKE :query ORDER BY name', array('query' => $query))->find_array();
    $res->write(json_encode($tags));
  }); 
});