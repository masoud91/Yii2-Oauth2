<?php

namespace infinitydesign\idcoauth\models\mysql;

use Yii;
use yii\db\ActiveRecord;
use OAuth2\Storage\ClientCredentialsInterface;

/**
 * This is the model class for table "oauth_clients".
 *
 * @property int $id
 * @property string $client_id
 * @property string $client_secret
 * @property string $redirect_uri
 * @property string $grant_types
 * @property string $scope
 * @property int $user_id
 * @property int $status
 * @property string $cdt
 * @property string $udt
 */
class OauthClients extends ActiveRecord implements ClientCredentialsInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oauth_clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['client_id', 'grant_types'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['cdt', 'udt'], 'safe'],
            [['client_id'], 'string', 'max' => 64],
            [['client_secret', 'grant_types'], 'string', 'max' => 256],
            [['redirect_uri', 'scope'], 'string', 'max' => 1024],
            [['client_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'client_id' => 'Client ID',
            'client_secret' => 'Client Secret',
            'redirect_uri' => 'Redirect Uri',
            'grant_types' => 'Grant Types',
            'scope' => 'Scope',
            'user_id' => 'User ID',
            'status' => 'Status',
            'cdt' => 'Cdt',
            'udt' => 'Udt',
        ];
    }

    /**
     * @param $client_id
     * @param null $client_secret
     * @return bool
     */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
        \Yii::warning("checkClientCredentials called");

        if( !$model = self::findOne(['client_id' => $client_id]) ){
            return false;
        }

        return $model->client_secret == $client_secret;
    }

    /**
     * @param $client_id
     * @return bool
     */
    public function isPublicClient($client_id)
    {
        \Yii::warning("isPublicClient called");

        if( !$model = self::findOne(['client_id' => $client_id]) ){
            return false;
        }

        return isset($model->client_secret) || empty($model->client_secret);
    }

    /**
     * @param $client_id
     * @return array|bool
     */
    public function getClientDetails($client_id)
    {
        \Yii::warning("getClientDetails called");

        if( !$model = self::findOne(['client_id' => $client_id]) ){
            return false;
        }

        Yii::warning($model->toArray());

        return $model->toArray();
    }

    /**
     * @param $client_id
     * @param null $client_secret
     * @param null $redirect_uri
     * @param null $grant_types
     * @param null $scope
     * @param null $user_id
     * @return bool
     */
    public function setClientDetails($client_id, $client_secret = null, $redirect_uri = null, $grant_types = null, $scope = null, $user_id = null)
    {
        \Yii::warning("setClientDetails called");

        $client = [
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'grant_types' => $grant_types,
            'scope' => $scope,
            'user_id' => $user_id,
        ];

        if ($this->getClientDetails($client_id)) {
            return (self::updateAll(['client_id' => $client_id], $client) > 0);
        }

        $client['client_id'] = $client_id;

        $model = new self($client);
        return $model->save();
    }

    /**
     * @param bool $isConfidential
     * @param null $userId
     * @param string $grantTypes
     * @param null $redirectUri
     * @param null $scope
     * @return bool
     * @throws yii\base\Exception
     */
    public static function addClient($isConfidential = false, $userId = null, $grantTypes = 'password', $redirectUri = null, $scope = null){
        $client = new self();
        if( $isConfidential ) {
            $cd = 'confidential-' . Yii::$app->security->generateRandomString(64);
            $client->client_id = $cd;
            $client->client_secret = Yii::$app->security->generateRandomString(128);
        } else {
            $cd = 'public-' . Yii::$app->security->generateRandomString(64);
            $client->client_id = $cd;
        }

        Yii::error($cd);

        if( $userId !== null ) {
            $client->user_id = $userId;
        }
        if( $redirectUri !== null ) {
            $client->redirect_uri = $redirectUri;
        }
        if( $scope !== null ) {
            $client->scope = $scope;
        }
        $client->grant_types = $grantTypes;

        return $client->save();
    }

    /**
     * @param $client_id
     * @param $grant_type
     * @return bool
     */
    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        Yii::warning("checkRestrictedGrantType called");

        $details = $this->getClientDetails($client_id);
        if (isset($details['grant_types'])) {
            $grant_types = explode(' ', $details['grant_types']);
            return in_array($grant_type, $grant_types);
        }
        // if grant_types are not defined, then none are restricted
        return true;
    }

    public function getClientScope($client_id)
    {
        \Yii::warning("getClientScope called");

        if (!$clientDetails = $this->getClientDetails($client_id)) {
            return false;
        }
        if (isset($clientDetails['scope'])) {
            return $clientDetails['scope'];
        }
        return null;
    }
}
