<?php

namespace infinitydesign\idcoauth;

use Yii;
use yii\mongodb\ActiveRecord;
use OAuth2\Storage\ScopeInterface;

/**
 * This is the model class for collection "oauth_scopes".
 *
 * @property \MongoDB\BSON\ObjectID|string $_id
 * @property mixed $scope
 * @property mixed $is_default
 */
class OauthScopes extends ActiveRecord implements ScopeInterface
{
    /**
     * {@inheritdoc}
     */
    public static function collectionName()
    {
        return 'oauth_scopes';
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            '_id',
            'scope',
            'is_default',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scope', 'is_default'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'ID'),
            'scope' => Yii::t('app', 'Scope'),
            'is_default' => Yii::t('app', 'Is Default'),
        ];
    }

    /**
     * Check if the provided scope exists in storage.
     *
     * @param string $scope - A space-separated string of scopes.
     * @return bool         - TRUE if it exists, FALSE otherwise.
     */
    public function scopeExists($scope)
    {
        return true;

        // uncomment next lines in order to implement functionality
        /*
        // Check reserved scopes first.
        $scope = explode(' ', trim($scope));
        $reservedScope = $this->getReservedScopes();
        $nonReservedScopes = array_diff($scope, $reservedScope);
        if (count($nonReservedScopes) == 0) {
            return true;
        } else {
            // Check the storage for non-reserved scopes.
            $nonReservedScopes = implode(' ', $nonReservedScopes);

            return $this->storage->scopeExists($nonReservedScopes);
        }
        */
    }

    /**
     * @param null $client_id
     * @return mixed
     */
    public function getDefaultScope($client_id = null)
    {
        return true;

        // uncomment next lines in order to implement functionality
        /*
        return $this->storage->getDefaultScope($client_id);
        */
    }
}
