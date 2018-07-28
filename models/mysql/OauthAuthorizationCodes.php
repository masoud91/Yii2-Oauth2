<?php

namespace infinitydesign\idcoauth\models\mysql;

use Yii;
use OAuth2\Storage\AuthorizationCodeInterface;

/**
 * This is the model class for table "oauth_authorization_codes".
 *
 * @property int $id
 * @property string $authorization_code
 * @property string $client_id
 * @property int $user_id
 * @property string $redirect_uri
 * @property int $expires
 * @property string $scope
 * @property string $id_token
 * @property string $code
 * @property string $device_id
 * @property int $status
 * @property string $cdt
 * @property string $udt
 */
class OauthAuthorizationCodes extends \yii\db\ActiveRecord implements AuthorizationCodeInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oauth_authorization_codes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['authorization_code', 'client_id', 'user_id', 'expires', 'device_id'], 'required'],
            [['user_id', 'expires', 'status'], 'integer'],
            [['cdt', 'udt'], 'safe'],
            [['authorization_code', 'client_id'], 'string', 'max' => 64],
            [['redirect_uri', 'scope'], 'string', 'max' => 1024],
            [['id_token', 'code'], 'string', 'max' => 128],
            [['device_id'], 'string', 'max' => 32],
            [['authorization_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'authorization_code' => 'Authorization Code',
            'client_id' => 'Client ID',
            'user_id' => 'User ID',
            'redirect_uri' => 'Redirect Uri',
            'expires' => 'Expires',
            'scope' => 'Scope',
            'id_token' => 'Id Token',
            'code' => 'Code',
            'device_id' => 'Device ID',
            'status' => 'Status',
            'cdt' => 'Cdt',
            'udt' => 'Udt',
        ];
    }

    /**
     * @param $code
     * @return OauthAuthorizationCodes|null
     */
    public function getAuthorizationCode($code)
    {
        \Yii::warning("getAuthorizationCode called");

        return self::findOne(['authorization_code' => $code]);
    }

    /**
     * @param string $code
     * @param mixed $client_id
     * @param mixed $user_id
     * @param string $redirect_uri
     * @param int $expires
     * @param null $scope
     * @param null $id_token
     * @return bool
     */
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
     * @return null|string
     * @throws \yii\base\Exception
     */
    public static function createToken($client_id, $user_id, $expires = 900) {

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

    /**
     * @param $code
     * @return bool
     */
    public function expireAuthorizationCode($code)
    {
        \Yii::warning("expireAuthorizationCode called");

        return ( self::deleteAll(['authorization_code' => $code]) > 0 );
    }
}
