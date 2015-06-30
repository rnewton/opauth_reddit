<?php
/**
 * Reddit strategy for Opauth
 * @see https://www.reddit.com/prefs/apps/
 * @see http://opauth.org
 * 
 * @copyright    Copyright © 2015 Robert Newton
 * @link         http://opauth.org
 * @package      Opauth.RedditStrategy
 * @license      MIT License
 */

class RedditStrategy extends OpauthStrategy {
    
    /**
     * Compulsory parameters
     */
    public $expects = array('key', 'secret');
    
    /**
     * Optional parameters
     */
    public $defaults = array(
        'method' => 'POST',         // The HTTP method being used. e.g. POST, GET, HEAD etc 
        'redirect_uri' => '{complete_url_to_strategy}oauth_callback',
        
        // For Reddit
        'authorize_url' => 'https://ssl.reddit.com/api/v1/authorize',
        'access_token_url' => 'https://ssl.reddit.com/api/v1/access_token',
        'reddit_profile_url' => 'http://api.reddit.com/api/v1/me'
    );
    
    /**
     * Auth request
     */
    public function request() 
    {
        $params = array(
            'key' => $this->strategy['key'],
            'secret' => $this->strategy['secret'],
            'redirect_uri' => $this->strategy['redirect_uri']
        );

        $this->clientGet($this->strategy['authorize_url'], $params);
    }

    /**
     * Receives oauth_verifier, requests for access_token and redirect to callback
     */
    public function oauth2callback() 
    {
        if (!empty($_GET['code'])) {
            $params = array(
                'client_id' => $this->strategy['key'],
                'client_secret' => $this->strategy['secret'],
                'redirect_uri' => $this->strategy['redirect_uri'],
                'code' => $_GET['code']
            );

            $response = $this->serverGet($this->strategy['access_token_url'], $params, null, $headers);
            $results = json_decode($response);

            if (!empty($results) && !empty($results->access_token)) {
                $userInfo = $this->userInfo($results->access_token);

                $this->auth = array(
                    'uid' => $userInfo['id'],
                    'info' => array(),
                    'credentials' => array(
                        'token' => $results->access_token,
                        'expires' => date('c', time() + $results->expires_in)
                    ),
                    'raw' => $userInfo
                );

                if (!empty($results->refresh_token)) {
                    $this->auth['credentials']['refresh_token'] = $results->refresh_token;
                }

                $this->mapProfile($userInfo, 'name', 'name');

                $this->callback();
            }
        } else {
            $error = array(
                'provider' => 'Reddit',
                'code' => $_GET['error'],
                'message' => $_GET['error_description'],
                'raw' => $_GET
            );

            $this->errorCallback($error);
        }
    }

    private function userInfo($access_token)
    {
        $userInfo = $this->serverGet($this->strategy['reddit_profile_url'], array('access_token' => $access_token), null, $headers);

        if (!empty($userInfo)) {
            return $this->recursiveGetObjectVars(json_decode($userInfo));
        } else {
            $error = array(
                'code' => 'userinfo_error', 
                'message' => 'Failed when attempting to query for user information',
                'raw' => array(
                    'response' => $userInfo,
                    'headers' => $headers
                )
            );

            $this->errorCallback($error);
        }
    }
}
