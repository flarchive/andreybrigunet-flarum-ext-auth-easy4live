<?php namespace AndreyBrigunet\Flarum\Auth\LDAP\Controllers;

use Exception;
use Flarum\Forum\Auth\ResponseFactory;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\Http\UrlGenerator;
use Flarum\Api\Client;
use Flarum\Forum\Auth\Registration;
use Flarum\User\LoginProvider;
use Flarum\User\User;
use Flarum\User\RegistrationToken;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Support\Arr;
use Laminas\Diactoros\Response\HtmlResponse;
use Illuminate\Contracts\View\Factory as ViewFactory;

class AuthController implements RequestHandlerInterface
{
	protected $response;
	protected $settings;
	protected $view;

	public function __construct(Client $api, ResponseFactory $response, SettingsRepositoryInterface $settings, ViewFactory $view)
	{
		$this->api = $api;
		$this->response = $response;
		$this->settings = $settings;
		$this->view = $view;
	}

	public function handle(Request $request): ResponseInterface
	{
		$provider = 'easy4live';

		$body = $request->getParsedBody();
		$params = Arr::only($body, ['identification', 'password']);
		$identification = Arr::get($params, 'identification');
		$password = Arr::get($params, 'password');

		
		$user = $this->getUser([
            'email' => $identification,
            'password' => $password
		]);


		if (is_string($user)) {
			$view = $this->view->make("flarum.forum::error.default")->with('message', $user);
			return new HtmlResponse($view->render(), 503);
		}

		if (User::where(Arr::only((array)$user, 'username'))->first()) {
			$user->username = $user->username ."_". rand(100,999);
		}

        if (LoginProvider::logIn($provider, $user->id) || User::where(Arr::only((array)$user, 'email'))->first()) {
			return $this->response->make(
				$provider,
				$user->id,
				function (Registration $registration) use ($user) {
					$registration
						->provide('username', $user->username)
						->provideTrustedEmail($user->email)
						->setPayload((array)$user);
				}
			);
        }

		$new_reg = new Registration();
		$new_reg->provide('username', $user->username)
				->provideTrustedEmail($user->email)
				->setPayload((array)$user);
		
		$token = RegistrationToken::generate($provider, $user->id, $new_reg->getProvided(), $new_reg->getPayload());
        $token->save();


		$this->api->withParentRequest($request)->withBody([
			"username" => $user->username,
			"email" => $user->email,
			"token" => $token->token
		])->post('/auth/register');


		return $this->response->make(
			$provider,
			$user->id,
			function (Registration $registration) use ($user) {
				$registration
					->provide('username', $user->username)
					->provideTrustedEmail($user->email)
					->setPayload((array)$user);
			}
		);
	}

	protected function getUser($data)
    {
		// $url = $this->getSetting("domain") . $this->getSetting("request");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://gamma.easy4live.com/api/request/");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($result);

		if (is_object($result)) {

			if ($result->status == "success") {
				return $result->data;
			}

			return $result->message;
		}

		return 'Currently down for maintenance. Please come back later.';
	}

	protected function getSetting($key): ?string
    {
        return $this->settings->get("easy4live-auth.{$key}");
    }
}
