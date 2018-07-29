<?php

namespace infinitydesign\idcoauth\models\mysql;

use common\models\User;
use Yii;
use OAuth2\Storage\RefreshTokenInterface;

/**
 * This is the model class for table "oauth_refresh_tokens".
 *
 * @property int $id
 * @property string $refresh_token
 * @property string $client_id
 * @property int $expires
 * @property string $scope
 * @property int $user_id
 * @property string $device_id
 * @property int $status
 * @property string $cdt
 * @property string $udt
 */
class OauthRefreshTokens extends \yii\db\ActiveRecord implements RefreshTokenInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oauth_refresh_tokens';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['refresh_token', 'client_id', 'expires', 'device_id'], 'required'],
            [['expires', 'user_id', 'status'], 'integer'],
            [['cdt', 'udt'], 'safe'],
            [['refresh_token', 'client_id'], 'string', 'max' => 64],
            [['scope'], 'string', 'max' => 1024],
            [['device_id'], 'string', 'max' => 32],
            [['refresh_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'refresh_token' => 'Refresh Token',
            'client_id' => 'Client ID',
            'expires' => 'Expires',
            'scope' => 'Scope',
            'user_id' => 'User ID',
            'device_id' => 'Device ID',
            'status' => 'Status',
            'cdt' => 'Cdt',
            'udt' => 'Udt',
        ];
    }

    /**
     * @param $refresh_token
     * @return null|static
     */
    public function getRefreshToken($refresh_token)
    {
        \Yii::warning("getRefreshToken called");
        if( !$token = self::findOne(['refresh_token' => $refresh_token]) ){
            Yii::error("could not find refresh token : $refresh_token");
            return null;
        }

        if( !$user = User::findOne($token->user_id) ) {
            Yii::error("could not find user with ID $token->user_id");
            return null;
        }

        if( $uuid = Yii::$app->request->post('uuid') ) {

            Yii::warning("getRefreshToken request contains UUID");

            if( $token->device_id != $uuid ) {
                Yii::error("provided UUID is not equal with device_id in associated with refresh token");
                return null;
            }

            Yii::$app->user->identity = $user;
            return $token;
        }

        Yii::$app->user->identity = $user;
        return $token;
    }

    /**
     * @param $refresh_token
     * @param $client_id
     * @param $user_id
     * @param $expires
     * @param null $scope
     * @return bool
     */
    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        \Yii::warning("setRefreshToken called");
        $token = [
            'refresh_token' => $refresh_token,
            'client_id' => $client_id,
            'user_id' => $user_id,
            'expires' => $expires,
//            'scope' => $scope
        ];

        if( Yii::$app->request->post('uuid') ) {

            Yii::warning("setRefreshToken request contains UUID");

            if( !$device = OauthDevice::findByUUID(Yii::$app->request->post('uuid')) ) {
                Yii::error("could not find device with UUID: " . Yii::$app->request->post('uuid'));
                return false;
            }

            $token['device_id'] = $device->uuid;
        }

        $model = new self($token);
        return $model->save();
    }

    /**
     * @param $refreshToken
     * @return bool
     */
    public function unsetRefreshToken($refreshToken)
    {
        \Yii::warning("unsetRefreshToken called");

        return ( self::deleteAll(['refresh_token' => $refreshToken]) > 0 );
    }

    /**
     * @param $deviceId
     * @return bool
     */
    public static function killSessionByDeviceId($deviceId){
        return ( self::deleteAll(['device_id' => $deviceId]) > 0 );
    }

    /**
     * @return bool
     */
    public static function garbageCollector(){
        return ( self::deleteAll(['<', 'expires', time()]) > 0 );
    }
}
