<?php
/**
 * Bitbucket
 * @package lib-user-auth-bitbucket
 * @version 0.0.1
 */

namespace LibUserAuthBitBucket\Library;

use LibCurl\Library\Curl;
use LibUserAuthBitBucket\Model\UserBitBucket as UBBucket;
use LibUser\Library\Fetcher;

class BitBucket
{
    protected static $error;

    protected static function setError(string $error)
    {
        self::$error = $error;

        return null;
    }

    public static function getAccount(string $token): ?object
    {
        $config = \Mim::$app->config->libUserAuthBitBucket;
        $client = $config->client;
        $api    = $config->api;

        $host   = $api->host;
        $path   = '/2.0/user';

        $curl = [
            'url' => $host . $path,
            'method' => 'GET',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token
            ]
        ];

        $result = Curl::fetch($curl);

        if(!$result || !is_object($result))
            return self::setError('Unable to reach bitbucket server');

        if(isset($result->error))
            return self::setError($result->error->message);

        $data = (object)[
            'bitbucket' => $result,
            'user' => null
        ];

        $account_id = $result->account_id;

        $bit_user = UBBucket::getOne([
            'bitbucket' => $account_id
        ]);
        if($bit_user){
            $data->user = Fetcher::getOne(['id' => $bit_user->user]);
        }

        return $data;
    }

    public static function getAuthURL(): string
    {
        $config = \Mim::$app->config->libUserAuthBitBucket;
        $client = $config->client;

        $host   = $client->host;
        $path   = '/site/oauth2/authorize?';
        $queries = [
            'client_id' => $client->id,
            'response_type' => 'code'
        ];

        $url = $host . $path . http_build_query($queries);

        return $url;
    }

    public static function getError(): string
    {
        return self::$error ?? '';
    }

    public static function refreshToken(string $r_token): ?string
    {
        $config = \Mim::$app->config->libUserAuthBitBucket;
        $client = $config->client;

        $host   = $client->host;
        $path   = '/site/oauth2/access_token';
        $body   = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $r_token,
            'client_secret' => $client->secret,
            'client_id'     => $client->id
        ];

        $curl = [
            'url' => $host . $path,
            'method' => 'POST',
            'headers' => [
                'Accept' => 'application/json'
            ],
            'body' => $body
        ];

        $result = Curl::fetch($curl);

        if(!$result || !is_object($result))
            return self::setError('Unable to reach bitbucket server');

        if(isset($result->error_description))
            return self::setError($result->error_description);

        return $result->access_token;
    }

    public static function setUser($bitbucket, $user): void
    {
        UBBucket::create([
            'user' => $user,
            'bitbucket' => $bitbucket
        ]);
    }

    public static function validateCode(string $code): ?object
    {
        $config = \Mim::$app->config->libUserAuthBitBucket;
        $client = $config->client;

        $host   = $client->host;
        $path   = '/site/oauth2/access_token';
        $body   = [
            'code'          => $code,
            'grant_type'    => 'authorization_code',
            'client_secret' => $client->secret,
            'client_id'     => $client->id
        ];

        $curl = [
            'url' => $host . $path,
            'method' => 'POST',
            'headers' => [
                'Accept' => 'application/json'
            ],
            'body' => $body
        ];

        $result = Curl::fetch($curl);

        if(!$result || !is_object($result))
            return self::setError('Unable to reach bitbucket server');

        if(isset($result->error_description))
            return self::setError($result->error_description);

        return $result;
    }
}
