<?php

namespace infinitydesign\idcoauth\mysql\models;

use Yii;

/**
 * This is the model class for table "oauth_device".
 *
 * @property int $id
 * @property string $uuid
 * @property string $pid
 * @property int $user_id
 * @property string $os
 * @property string $os_version
 * @property string $phone_model
 * @property string $app_version
 * @property int $status
 * @property string $cdt
 * @property string $udt
 */
class OauthDevice extends \yii\db\ActiveRecord
{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oauth_device';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['cdt', 'udt'], 'safe'],
            [['uuid', 'phone_model'], 'string', 'max' => 64],
            [['pid'], 'string', 'max' => 256],
            [['os'], 'string', 'max' => 32],
            [['os_version', 'app_version'], 'string', 'max' => 16],
            [['uuid'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uuid',
            'pid' => 'Pid',
            'user_id' => 'User ID',
            'os' => 'Os',
            'os_version' => 'Os Version',
            'phone_model' => 'Phone Model',
            'app_version' => 'App Version',
            'status' => 'Status',
            'cdt' => 'Cdt',
            'udt' => 'Udt',
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
