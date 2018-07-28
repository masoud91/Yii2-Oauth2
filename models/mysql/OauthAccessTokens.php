<?php

namespace infinitydesign\idcoauth\models\mysql;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "oauth_access_tokens".
 *
 * @property int $id
 * @property string $access_token
 * @property string $client_id
 * @property int $expires
 * @property string $scope
 * @property int $user_id
 * @property string $device_id
 * @property int $status
 * @property string $cdt
 * @property string $udt
 */
class OauthAccessTokens extends ActiveRecord
{

    const OS_ANDROID = 'Android';
    const OS_IOS = 'Ios';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oauth_access_tokens';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['access_token', 'client_id', 'expires', 'device_id'], 'required'],
            [['expires', 'user_id', 'status'], 'integer'],
            [['cdt', 'udt'], 'safe'],
            [['access_token', 'client_id'], 'string', 'max' => 64],
            [['scope'], 'string', 'max' => 1024],
            [['device_id'], 'string', 'max' => 32],
            [['access_token'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'access_token' => 'Access Token',
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
     * @param $access_token
     * @return null|static
     */
    public function getAccessToken($access_token)
    {
        \Yii::warning("getAccessToken called");
        return self::findOne(['access_token' => $access_token]);

    }

    /**
     * @param $access_token
     * @param $client_id
     * @param $user_id
     * @param $expires
     * @param null $scope
     * @return bool
     */
    public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
    {
        Yii::warning("setAccessToken called");

        $token = [
            'client_id' => $client_id,
            'expires' => $expires,
            'user_id' => $user_id,
            'scope' => $scope
        ];

        if( Yii::$app->request->post('uuid') ) {

            Yii::warning("setAccessToken request contains UUID");

            if( !$device = OauthDevice::findByUUID(Yii::$app->request->post('uuid')) ) {
                Yii::error("could not find device with UUID: " . Yii::$app->request->post('uuid'));
                return false;
            }

            $token['device_id'] = $device->uuid;
        }

        Yii::warning(yii\helpers\Json::encode($token));

        // if it exists, update it.
        if ($this->getAccessToken($access_token)) {
            return ( self::updateAll($token, ['access_token' => $access_token]) > 0 );
        }

        $token['access_token'] = $access_token;

        $model = new self($token);
        return $model->save();
    }

    /**
     * @param $accessToken
     * @return bool
     */
    public function unsetAccessToken($accessToken)
    {
        \Yii::warning("unsetAccessToken called");

        return ( self::deleteAll(['access_token' => $accessToken]) > 0 );
    }

    /**
     * @param $accessToken
     * @return bool|int
     */
    public static function getUserIdByAccessToken($accessToken){
        if( !$accessTokenModel = self::findOne(['access_token' => $accessToken]) ){
            Yii::error("could not find access token $accessToken");
            return false;
        }

        Yii::error(yii\helpers\Json::encode($accessTokenModel));
        return $accessTokenModel->user_id;
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
