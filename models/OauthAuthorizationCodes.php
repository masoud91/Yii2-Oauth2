<?php

namespace infinitydesign\idcoauth;

use Yii;
use yii\mongodb\ActiveRecord;
use OAuth2\Storage\AuthorizationCodeInterface;

/**
 * This is the model class for collection "oauth_authorization_codes".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property string $authorization_code
 * @property string $client_id
 * @property \MongoDB\BSON\ObjectID|string $user_id
 * @property string $redirect_uri
 * @property integer $expires
 * @property string $scope
 * @property string $id_token
 * @property string $code

 * @property string $device_id
 * @property mixed $cdt
 * @property mixed $udt
 */
class OauthAuthorizationCodes extends ActiveRecord implements AuthorizationCodeInterface
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'oauth_authorization_codes';
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'authorization_code',
            'client_id',
            'user_id',
            'redirect_uri',
            'expires',
            'scope',
            'id_token',
            'code',

            'device_id',
            'cdt',
            'udt',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'authorization_code',
                'client_id',
                'user_id',
                'redirect_uri',
                'expires',
                'scope',
                'id_token',
                'code'
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'ID'),
            'authorization_code' => Yii::t('app', 'Authorization Code'),
            'client_id' => Yii::t('app', 'Client ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'redirect_uri' => Yii::t('app', 'Redirect Uri'),
            'expires' => Yii::t('app', 'Expires'),
            'scope' => Yii::t('app', 'Scope'),
            'id_token' => Yii::t('app', 'Id Token'),
            'code' => Yii::t('app', 'Code')
        ];
    }

    public function getAuthorizationCode($code)
    {
        \Yii::warning("getAuthorizationCode called");

        return self::findOne(['authorization_code' => $code]);
    }

    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        \Yii::warning("setAuthorizationCode called");

        $token = [
            'authorization_code' => $code,
            'client_id' => $client_id,
            'user_id' => $user_id,
            'redirect_uri' => $redirect_uri,
            'expires' => $expires,
            'scope' => $scope,
            'id_token' => $id_token,
        ];

        // if it exists, update it.
        if ($this->getAuthorizationCode($code)) {
            return ( self::updateAll(['authorization_code' => $code], $token) > 0 );
        }

        $model = new self($token);
        return $model->save();
    }

    /**
     * @param $client_id
     * @param $user_id
     * @param int $expires
     * @return mixed|null
     */
    public static function createToken($client_id, $user_id, $expires = 60*15) {

        Yii::info("OAuthAuthorizationCodes::createToken() called.");

        $token = [
            'authorization_code' => Yii::$app->security->generateRandomString(128),
            'client_id' => $client_id,
            'user_id' => $user_id,
            'expires' => time() + $expires,
        ];

        $model = new self($token);
        if( !$model->save() ) {
            Yii::error("could not save new OAuthAuthorizationCodes");
            return null;
        }

        return $model->authorization_code;
    }

    public function expireAuthorizationCode($code)
    {
        \Yii::warning("expireAuthorizationCode called");

        return ( self::deleteAll(['authorization_code' => $code]) > 0 );
    }
}
