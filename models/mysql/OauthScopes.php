<?php

namespace infinitydesign\idcoauth\models\mysql;

use Yii;
use yii\db\ActiveRecord;
use OAuth2\Storage\ScopeInterface;

/**
 * This is the model class for table "oauth_scopes".
 *
 * @property int $id
 * @property string $scope
 * @property int $is_default
 * @property int $status
 * @property string $cdt
 * @property string $udt
 */
class OauthScopes extends ActiveRecord implements ScopeInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'oauth_scopes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_default', 'status'], 'integer'],
            [['cdt', 'udt'], 'safe'],
            [['scope'], 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'scope' => 'Scope',
            'is_default' => 'Is Default',
            'status' => 'Status',
            'cdt' => 'Cdt',
            'udt' => 'Udt',
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
