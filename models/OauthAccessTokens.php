<?php

namespace infinitydesign\idcoauth\models;

use yii;

use common\idco\mongodb\ActiveRecord;
use common\components\MongoDateBehavior;

use OAuth2\Storage\AccessTokenInterface;
use MongoDB\BSON\ObjectId;

/**
 * This is the model class for collection "OauthClients".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property string $access_token
 * @property string $client_id
 * @property \MongoDB\BSON\ObjectID|string $user_id
 * @property integer $expires
 * @property string $scope
 * @property string $device_id
 * @property integer $cdt
 * @property integer $udt
 */
class OauthAccessTokens extends ActiveRecord implements AccessTokenInterface
{
    
    const OS_ANDROID = 'Android';
    const OS_IOS = 'Ios';

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'oauth_access_tokens';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'access_token',
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['access_token', 'client_id', 'expires', 'scope', 'user_id', 'device_id', 'cdt', 'udt'], 'safe'],
            [['access_token'], 'required'],
            ['access_token', 'unique',
                'targetClass' => 'infinitydesign\idcoauth\models\OauthClients',
                'message' => 'Repeated access_token.'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'ID'),
            'access_token' => Yii::t('app', 'Access Token'),
            'client_id' => Yii::t('app', 'Client ID'),
            'expires' => Yii::t('app', 'Expires'),
            'scope' => Yii::t('app', 'Scope'),
            'user_id' => Yii::t('app', 'User ID'),
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
            'user_id' => new ObjectId($user_id),
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
     * @return bool|\MongoDB\BSON\ObjectID|string
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
