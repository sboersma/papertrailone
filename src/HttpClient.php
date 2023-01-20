<?php

namespace Papertrail;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Uri;
use \League\OAuth2\Client\Token\AccessToken;
use Unsplash\OAuth2\Client\Provider\Unsplash;
use GuzzleHttp\Psr7\Request;

/**
 * Class HttpClient
 * @package Unsplash
 */
class HttpClient
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $host = 'api.papertrail.one';

    /**
     * @var string
     */
    private $scheme = 'https';

    public static $utmSource = 'api_app';

    /**
     * @var null
     */
    private $authorization = null;

    /**
     * Unsplash\Connection object link to the HttpClient
     * Need to be set on the class before running anything else
     *
     * @example Unsplash\HttpClient::$connection = new Unsplash\Connection();
     * @var Connection
     */
    public static $connection;

    /**
     * Generate a new http client. Retrieve the authorization token generated by the $connection object
     */
    public function __construct()
    {
        $envPath = __DIR__ . '/../tests/';
        if (class_exists('\Dotenv\Dotenv') && file_exists($envPath . ".env")) {
            $dotenv = \Dotenv\Dotenv::createImmutable($envPath);
            $dotenv->load();

            $this->scheme = getenv('HTTP_SCHEME');
            $this->host = 'api.' . getenv('HOST');
        }

        $this->httpClient = new Client(['handler' => $this->setHandler(self::$connection->getAuthorizationToken())]);
    }

    /**
     * Initialize the $connection object that is used for all requests to the API
     *
     * $credentials array Credentials needed for the API request
     * ['applicationId'] string Application id. This value is needed across all requests
     * ['secret'] string Application secret. Application secret is needed for OAuth authentication
     * ['callbackUrl'] string Callback url. After OAuth authentication, the user will be redirected to this url.
     * ['utmSource'] string Name of your application. This is required, a notice will be raised if missing
     *
     * $accessToken array Access Token information
     * ['access_token'] string Access Token identifier
     * ['refresh_token'] string Refresh Token necessary when the access token is expired
     * ['expires_in'] int Define in when the access token will expire
     *
     * @param  array $credentials see above
     * @param  array| \League\OAuth2\Client\Token\accessToken $accessToken     see above
     * @return void
     */
    public static function init($credentials = [], $accessToken = [])
    {
        $token = null;
        if (! empty($accessToken)) {
            $token = self::initAccessToken($accessToken);
        }

        if (!isset($credentials['utmSource']) || empty($credentials['utmSource'])) {
            $terms = "https://community.unsplash.com/developersblog/unsplash-api-terms-explained#block-yui_3_17_2_1_1490972762425_202608";
            trigger_error("utmSource is required as part of API Terms: {$terms}");
        } else {
            self::$utmSource = $credentials['utmSource'];
        }

        self::$connection = new Connection(self::initProvider($credentials), $token);
    }

    /**
     * Create an unsplash provider from the credentials provided by the user.
     * If only the `applicationId` is set, non-public scoped permissions
     * won't work since access tokens can't be created without the secret and callback url
     *
     * @param array $credentials
     * @return Unsplash Provider object used for the authentication
     * @see HttpClient::init documentation
     */
    private static function initProvider($credentials = [])
    {
        return new Unsplash([
            'clientId' => isset($credentials['applicationId']) ? $credentials['applicationId'] : null,
            'clientSecret' => isset($credentials['secret']) ? $credentials['secret'] : null,
            'redirectUri' => isset($credentials['callbackUrl']) ? $credentials['callbackUrl'] : null
        ]);
    }

    /**
     * Create an Access Token the provider can use for authentication
     *
     * @param   mixed $accessToken     see HttpClient::init documentation
     * @return \League\OAuth2\Client\Token\AccessToken | null
     */
    private static function initAccessToken($accessToken)
    {
        if (is_array($accessToken)) {
            return new AccessToken($accessToken);
        } elseif (is_a($accessToken, '\League\OAuth2\Client\Token\AccessToken')) {
            return $accessToken;
        } else {
            return null;
        }
    }

    /**
     * Send an http request through the http client.
     *
     * @param  string $method http method sent
     * @param  array $arguments Array containing the URI to send the request and the parameters of the request
     * @return \GuzzleHttp\Psr7\Response
     */
    public function send($method, $arguments)
    {
        $uri = $arguments[0];
        $params = isset($arguments[1]) ? $arguments[1] : [];
        if (substr($uri, 0, 1) !== '/') {
            $uri = '/' . $uri;
        }

        $headers = [
            "Accept-Encoding" => "gzip"
        ];

        $response = $this->httpClient->send(
            new Request($method, new Uri($uri), $headers),
            $params
        );

        return $response;
    }

    /**
     * Generate a new handler that will manage the HTTP requests.
     *
     * Some middleware are also configured to manage the authorization header and request URI
     *
     * @param string $authorization Authorization code to pass in the header
     * @return \GuzzleHttp\HandlerStack
     */
    private function setHandler($authorization)
    {
        $stack = new HandlerStack();

        $stack->setHandler(new CurlHandler());

        // Set authorization headers
        $this->authorization = $authorization;
        $stack->push(Middleware::mapRequest(function (Request $request) {
            return $request->withHeader('Authorization', $this->authorization);
        }), 'set_authorization_header');

        // Set the request ui
        $stack->push(Middleware::mapRequest(function (Request $request) {
            $uri = $request->getUri()->withHost($this->host)->withScheme($this->scheme);

            return $request->withUri($uri);
        }), 'set_host');

        return $stack;
    }
}
