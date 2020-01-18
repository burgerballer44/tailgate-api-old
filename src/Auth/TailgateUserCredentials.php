<?php

namespace TailgateApi\Auth;

use OAuth2\GrantType\UserCredentials;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use TailgateApi\Events\UserLoggedIn;
use Burger\EventPublisher;

// extend oauth2 UserCredentials implementation since we need our implementations
class TailgateUserCredentials extends UserCredentials
{
    private $userInfo;

    public function validateRequest(RequestInterface $request, ResponseInterface $response)
    {
        // assuming things have not changed... username is the email address
        if (!$request->request("password") || !$request->request("username")) {
            $response->setError(400, 'invalid_request', 'Missing parameters: "username" and "password" required');

            return null;
        }

        if (!$this->storage->checkUserCredentials($request->request("username"), $request->request("password"))) {
            $response->setError(401, 'invalid_grant', 'Invalid email and password combination');

            return null;
        }

        $userInfo = $this->storage->getUserDetails($request->request("username"));

        if (empty($userInfo)) {
            $response->setError(400, 'invalid_grant', 'Unable to retrieve user information');

            return null;
        }

        if (!isset($userInfo['user_id'])) {
            throw new \LogicException("you must set the user_id on the array returned by getUserDetails");
        }

        $this->userInfo = $userInfo;

        $event = new UserLoggedIn($userInfo['user_id']);
        EventPublisher::instance()->publish(get_class($event), $event);

        return true;
    }

    public function getUserId()
    {
        return $this->userInfo['user_id'];
    }

    public function getScope()
    {
        return isset($this->userInfo['scope']) ? $this->userInfo['scope'] : null;
    }
}