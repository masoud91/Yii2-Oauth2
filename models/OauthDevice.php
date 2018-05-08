<?php

namespace infinitydesign\idcoauth\models;

use yii;
use yii\mongodb\ActiveRecord;
use common\components\MongoDateBehavior;

/**
 * This is the model class for collection "OauthClients".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property string $uuid
 * @property string $pid
 * @property string $user_id
 * @property string $os
 * @property string $os_version
 * @property string $phone_model
 * @property string $app_version
 * @property integer $status
 * @property integer $cdt
 */
class OauthDevice extends ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'oauth_device';
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return [
            '_id',
            'uuid',
            'pid',
            'user_id',
            'os',
            'os_version',
            'phone_model',
            'app_version',
            'status',
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
            [['uuid', 'pid', 'user_id', 'os', 'os_version', 'phone_model', 'app_version', 'status', 'cdt', 'udt'], 'safe'],

            [['uuid'], 'required'],

            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],

            ['uuid', 'unique',
                'targetClass' => 'infinitydesign\idcoauth\models\OauthDevice',
                'message' => 'This UUID has already been taken.'
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
            'uuid' => Yii::t('app', 'UUID'),
            'pid' => Yii::t('app', 'Player ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'os' => Yii::t('app', 'OS'),
            'os_version' => Yii::t('app', 'Os Version'),
            'phone_model' => Yii::t('app', 'Phone Model'),
            'app_version' => Yii::t('app', 'App Version'),
            'status' => Yii::t('app', 'Status'),
            'cdt' => Yii::t('app', 'Created At'),
            'udt' => Yii::t('app', 'Updated At'),
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
     * @param $uuid
     * @return static
     */
    public static function findByUUID($uuid){
        return self::findOne(['uuid' => $uuid, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @return bool|string
     */
    public static function upsert(){

        Yii::warning("device::upsert called");

        $request = Yii::$app->request;
        Yii::warning($request->post());

        if( !$uuid = $request->post('uuid') ){
            Yii::error('uuid is not provided');
            return false;
        }

        if( !$device = self::findByUUID($uuid) ){
            Yii::error("could not find device with UUID: $uuid");
            $device = new self();
            $device->uuid = $uuid;
        }

        $device->pid = $request->post('pid');
        $device->os = $request->post('os');
        $device->os_version = $request->post('os_version');
        $device->phone_model = $request->post('phone_model');
        $device->app_version = $request->post('app_version');
        $device->user_id = Yii::$app->user->id;

        if( !$device->save() ){
            Yii::error("could not save device with UUID: $uuid, errors:");
            Yii::error($device->errors);
            return false;
        }

        Yii::warning("device saved with UUID: $device->uuid");
        return $device->uuid;
    }

}
