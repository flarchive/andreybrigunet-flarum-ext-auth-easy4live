<?php namespace TitusPiJean\Flarum\Auth\LDAP;

use Flarum\Extend;
use Flarum\Foundation\Application;
use Illuminate\Events\Dispatcher;

return [
  (new Extend\Locales(__DIR__ . '/locale')),

  (new Extend\Frontend('admin'))
    ->js(__DIR__.'/js/dist/admin.js'),

  (new Extend\Frontend('forum'))
    ->js(__DIR__.'/js/dist/forum.js')
    ->css(__DIR__.'/less/forum.less'),

  (new Extend\Routes('forum'))
    ->post('/auth/with', 'auth.with.post', Controllers\AuthController::class),

  (new Extend\Routes('api'))
    ->post('/auth/register', 'auth.register.post', \Flarum\Forum\Controller\RegisterController::class),

  (new Extend\Settings)
    ->serializeToForum('easy4live-auth.onlyUse', 'easy4live-auth.onlyUse', 'boolVal', false)
    ->serializeToForum('easy4live-auth.method_name', 'easy4live-auth.method_name', 'strVal', 'LDAP'),
];
