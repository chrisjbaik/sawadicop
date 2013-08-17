<?php
  $app->get('/home', $acl_middleware(), function () use ($app) {
    $user = $_SESSION['user'];
    $groups = $user->groups()->find_many();
    $app->render('groups/list.php', array(
      'groups' => $groups
    ));
  });

  $app->get('/settings', $acl_middleware(), function () use ($app) {
    if (!empty($_SESSION['user'])) {
      $hybridauths = Model::factory('Hybridauth')->where('user_id', $_SESSION['user']->id)->find_many();
      $app->render('settings.php', array(
        'hybridauths' => $hybridauths
      ));
    } else {
      $app->redirect('/');
    }
  });