<?php

namespace infinitydesign\idcoauth\models\mongodb;

use MongoDB\BSON\ObjectId;
use Yii;
use yii\mongodb\ActiveRecord;
use OAuth2\Storage\RefreshTokenInterface;
use common\components\MongoDateBehavior;

/**
 * This is the model class for collection "oauth_refresh_tokens".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $refresh_token
 * @property mixed $client_id
 * @property mixed $user_id
 * @property mixed $expires
 * @property mixed $scope
 * @property string $device_id
 * @property integer $cdt
 * @property integer $udt
 */
class OauthRefreshTokens extends ActiveRecord implements RefreshTokenInterface
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'oauth_refresh_tokens';
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'refresh_token',
            'client_id',
            'user_id',
            'expires',
            'scope',

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
                'refresh_token',
                'client_id',
                'user_id',
                'expires',
                'scope',
                'device_id',
                'cdt',
                'udt'
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
            'refresh_token' => Yii::t('app', 'Refresh Token'),
            'client_id' => Yii::t('app', 'Client ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'expires' => Yii::t('app', 'Expires'),
            'scope' => Yii::t('app', 'Scope'),
            'device_id' => Yii::t('app', 'Device ID'),
            'cdt' => Yii::t('app', 'Created At'),
            'udt' => Yii::t('app', 'Updated At')
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => MongoDateBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cdt', 'udt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['udt']
                ]
            ]
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

        if( $uuid = Yii::$app->request->post('uuid') ) {

            Yii::warning("getRefreshToken request contains UUID");

            if( $token->device_id != $uuid ) {
                Yii::error("provided UUID is not equal with device_id in associated with refresh token");
                return null;
            }

            return $token;
        }

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
            'user_id' => new ObjectId($user_id),
            'expires' => $expires,
            'scope' => $scope
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

        return ( self::deleteAll(['access_token' => $refreshToken]) > 0 );
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
