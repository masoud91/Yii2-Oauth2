<?php

namespace infinitydesign\idcoauth;

use Yii;
use yii\mongodb\ActiveRecord;
use OAuth2\Storage\JwtBearerInterface;

/**
 * This is the model class for collection "oauth_jwt".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $client_id
 * @property mixed $subject
 * @property mixed $public_key
 */
class OauthJwt extends ActiveRecord implements JwtBearerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'oauth_jwt';
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'client_id',
            'subject',
            'public_key',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'subject', 'public_key'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'ID'),
            'client_id' => Yii::t('app', 'Client ID'),
            'subject' => Yii::t('app', 'Subject'),
            'public_key' => Yii::t('app', 'Public Key'),
        ];
    }

    public function getClientKey($client_id, $subject)
    {
        // TODO: Implement getClientKey() method.
    }


    public function getJti($client_id, $subject, $audience, $expiration, $jti)
    {
        // TODO: Implement getJti() method.
    }

    public function setJti($client_id, $subject, $audience, $expiration, $jti)
    {
        // TODO: Implement setJti() method.
    }
}
