<?php
  $app->group('/admin', $acl_middleware('admin'), function () use ($app) {
    $app->get('/', function () use ($app) {
      $app->render('admin/index.php', array(
        'page_title' => 'System Admin'
      ));
    });
    $app->group('/groups', function () use ($app) {
      $app->get('/', function () use ($app) {
        $groups = Model::factory('Group')->find_many();
        $app->render('admin/groups.php', array(
          'groups' => $groups
        ));
      });
      $app->delete('/:id', function ($id) use ($app) {
        $group = Model::factory('Group')->find_one($id);
        if ($group) {
          $group->delete();
          $app->flash('success', 'Group was successfully deleted!');
          $app->redirect('/admin/groups');
        } else {
          $app->flash('error', 'Group does not exist.');
          $app->redirect('/admin/groups');
        }
      });
    });
    $app->group('/users', function () use ($app) {
      $app->get('/', function () use ($app) {
        $users = Model::factory('User')->find_many();
        $app->render('admin/users.php', array(
          'users' => $users
        ));
      });
      $app->get('/:id/masquerade', function ($id) use ($app) {
        $user = Model::factory('User')->find_one($id);
        if ($user) {
          $_SESSION['user'] = $user;
          $app->redirect('/');
        } else {
          $app->flash('error', 'User does not exist.');
          $app->redirect('/admin/users');
        }
      });
      $app->delete('/:id', function ($id) use ($app) {
        $user = Model::factory('User')->find_one($id);
        if ($user) {
          $user->delete();
          $app->flash('success', 'User was successfully deleted!');
          $app->redirect('/admin/users');
        } else {
          $app->flash('error', 'User does not exist.');
          $app->redirect('/admin/users');
        }
      });
    });
    $app->group('/invites', function () use ($app) {
      $app->get('/', function () use ($app) {
        $requests = Model::factory('Invite')->where('admin_approved', 0)->find_many();
        $invites = Model::factory('Invite')->where('admin_approved', 1)->where('redeemed', 0)->find_many();
        $app->render('admin/invites.php', array(
          'requests' => $requests,
          'invites' => $invites
        ));
      });
      $app->get('/:id/approve', function ($id) use ($app) {
        $request = Model::factory('Invite')->find_one($id);
        if (!$request) {
          $app->flash('error', 'Invite does not exist.');
          $app->redirect('/admin/invites');
        } else {
          $request->admin_approved = 1;
          if ($request->save()) {
            $app->flash('success', 'Invite approved!');
          } else {
            $app->flash('error', 'Invite approval failed.');
          }
          $app->redirect('/admin/invites');
        }
      });
    });
  });