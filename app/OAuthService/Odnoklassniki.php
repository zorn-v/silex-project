<?php
namespace App\OAuthService;

use OAuth\OAuth2\Service\AbstractService;
use OAuth\OAuth2\Token\StdOAuth2Token;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Consumer\Credentials;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Token\TokenInterface;
use OAuth\Common\Token\Exception\ExpiredTokenException;

class Odnoklassniki extends AbstractService
{

    protected $applicationKey;

    public function __construct(Credentials $credentials, ClientInterface $httpClient, TokenStorageInterface $storage, $scopes = array(), UriInterface $baseApiUri = null)
    {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri);
        if( null === $baseApiUri ) {
            $this->baseApiUri = new Uri('https://api.ok.ru/api/');
        }
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://connect.ok.ru/oauth/authorize');
    }

    /**
     * @return \OAuth\Common\Http\Uri\UriInterface
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://api.ok.ru/oauth/token.do');
    }

    /**
     * @param string $appKey
     * @return $this
     */
    public function setApplicationKey($appKey)
    {
        $this->applicationKey = $appKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getApplicationKey()
    {
        return $this->applicationKey;
    }

    /**
     * @param string $responseBody
     * @return \OAuth\Common\Token\TokenInterface|\OAuth\OAuth2\Token\StdOAuth2Token
     * @throws \OAuth\Common\Http\Exception\TokenResponseException
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if( null === $data || !is_array($data) ) {
            throw new TokenResponseException('Unable to parse response.');
        } elseif( isset($data['error'] ) ) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();

        $token->setAccessToken( $data['access_token'] );
        $token->setLifeTime(1800); // token has fixed expire and it's value is not returned by service

        if( isset($data['refresh_token'] ) ) {
            $token->setRefreshToken( $data['refresh_token'] );
            unset($data['refresh_token']);
        }

        unset( $data['access_token'] );

        if ( $this->applicationKey ) {
            $data['application_key'] = $this->applicationKey;
        }
        //unset( $data['expires_in'] );
        $token->setExtraParams( $data );

        return $token;
    }

    public function request($path, $method = 'GET', $body = null, array $extraHeaders = array())
    {
        $uri = $this->determineRequestUriFromPath($path, $this->baseApiUri);
        $token = $this->storage->retrieveAccessToken($this->service());
        $extraParams = $token->getExtraParams();

        if( ( $token->getEndOfLife() !== TokenInterface::EOL_NEVER_EXPIRES ) &&
            ( $token->getEndOfLife() !== TokenInterface::EOL_UNKNOWN ) &&
            ( time() > $token->getEndOfLife() ) ) {

            throw new ExpiredTokenException('Token expired on ' . date('m/d/Y', $token->getEndOfLife()) . ' at ' . date('h:i:s A', $token->getEndOfLife()) );
        }

        if ( strpos($path, '?') ) {
            $query = explode('?', $path);
            $query = end($query);
            parse_str($query, $queryParams);
            $body = array_merge((array)$body, $queryParams);
        }

        ksort($body);

        $sig = '';
        foreach ($body as $k=>$v) {
            $sig .= $k . '=' . $v;
        }
        $sig = md5($sig . md5( $token->getAccessToken() . $this->credentials->getConsumerSecret() ) );
        $body['sig'] = $sig;

        $uri->addToQuery( 'access_token', $token->getAccessToken() );
        foreach ($body as $qK => $qV) {
            $uri->addToQuery( $qK, $qV );
        }

        $body = array();

        $extraHeaders = array_merge( $this->getExtraApiHeaders(), $extraHeaders );

        return $this->httpClient->retrieveResponse($uri, $body, $extraHeaders, $method);
    }


    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_QUERY_STRING;
    }
}