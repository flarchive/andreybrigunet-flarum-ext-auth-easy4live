<?php namespace AndreyBrigunet\Flarum\Auth;

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
    ->post('/auth/with', 'auth.with.forum', Controllers\AuthController::class),

  (new Extend\Routes('api'))
    ->post('/auth/register', 'auth.register.api', \Flarum\Forum\Controller\RegisterController::class),

  (new Extend\Settings)
    ->serializeToForum('andreybrigunet-auth-easy4live.onlyUse', 'andreybrigunet-auth-easy4live.onlyUse', 'boolVal', false),
];
