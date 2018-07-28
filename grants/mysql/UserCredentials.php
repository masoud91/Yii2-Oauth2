<?php

namespace infinitydesign\idcoauth\grants;

use Yii;
use infinitydesign\idcoauth\models\mysql\OauthDevice;
use OAuth2\Storage\UserCredentialsInterface;
use OAuth2\ResponseType\AccessTokenInterface;
use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use LogicException;

/**
 * @author Brent Shaffer <bshafs at gmail dot com>
 */
class UserCredentials implements GrantTypeInterface
{
    /**
     * @var array
     */
    private $userInfo;

    /**
     * @var UserCredentialsInterface
     */
    protected $storage;

    /**
     * @param UserCredentialsInterface $storage - REQUIRED Storage class for retrieving user credentials information
     */
    public function __construct(UserCredentialsInterface $storage)
    {
        Yii::warning('__construct');
        $this->storage = $storage;
    }

    /**
     * @return string
     */
    public function getQueryStringIdentifier()
    {
        Yii::warning('getQueryStringIdentifier');
        return 'password';
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @return bool|mixed|null
     *
     * @throws LogicException
     */
    public function validateRequest(RequestInterface $request, ResponseInterface $response)
    {
        Yii::warning('validateRequest');

        if (!$request->request("password") || !$request->request("username") || !$request->request("uuid") ) {
            $response->setError(401, 'invalid_request', 'Missing parameters: "username", "password" and "uuid" required');

            return null;
        }

        if (!$this->storage->checkUserCredentials($request->request("username"), $request->request("password"))) {
            $response->setError(401, 'invalid_grant', 'Invalid username and password combination');

            return null;
        }

        if( !$uuid = OauthDevice::upsert() ) {
            Yii::error("could not upsert device.");
            $response->setError(401, 'invalid_grant', 'Could not upsert device.');
            return null;
        }

        $userInfo = $this->storage->getUserDetails($request->request("username"));

        if (empty($userInfo)) {
            $response->setError(401, 'invalid_grant', 'Unable to retrieve user information');

            return null;
        }

        if (!isset($userInfo['user_id'])) {
            throw new \LogicException("you must set the user_id on the array returned by getUserDetails");
        }

        $this->userInfo = $userInfo;

        return true;
    }

    /**
     * Get client id
     *
     * @return mixed|null
     */
    public function getClientId()
    {
        Yii::warning('getClientId');
        return null;
    }

    /**
     * Get user id
     *
     * @return mixed
     */
    public function getUserId()
    {
        Yii::warning('getUserId');
        return $this->userInfo['user_id'];
    }

    /**
     * Get scope
     *
     * @return null|string
     */
    public function getScope()
    {
        Yii::warning('getScope');
        return isset($this->userInfo['scope']) ? $this->userInfo['scope'] : null;
    }

    /**
     * Create access token
     *
     * @param AccessTokenInterface $accessToken
     * @param mixed                $client_id   - client identifier related to the access token.
     * @param mixed                $user_id     - user id associated with the access token
     * @param string               $scope       - scopes to be stored in space-separated string.
     * @return array
     */
    public function createAccessToken(AccessTokenInterface $accessToken, $client_id, $user_id, $scope)
    {
        Yii::warning('createAccessToken');
        return $accessToken->createAccessToken($client_id, $user_id, $scope);
    }
}
