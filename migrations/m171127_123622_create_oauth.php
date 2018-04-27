<?php

class m171127_123622_create_oauth extends \yii\mongodb\Migration
{

    public function up()
    {
        $this->createCollection('oauth_clients');
        $this->createIndex('oauth_clients', 'client_id', [
            'unique' => true,
            'name' => 'oauth_clients_index_client_id'
        ]);

        $this->createCollection('oauth_access_tokens');
        $this->createIndex('oauth_access_tokens', 'access_token', [
            'unique' => true,
            'name' => 'oauth_access_tokens_index_access_token'
        ]);

        $this->createCollection('oauth_authorization_codes');
        $this->createIndex('oauth_authorization_codes', 'authorization_code', [
            'unique' => true,
            'name' => 'oauth_authorization_codes_index_authorization_code'
        ]);

        $this->createCollection('oauth_refresh_tokens');
        $this->createIndex('oauth_refresh_tokens', 'refresh_token', [
            'unique' => true,
            'name' => 'oauth_refresh_tokens_index_refresh_token'
        ]);


        $this->createCollection('oauth_scopes');
        $this->createIndex('oauth_scopes', 'scope', [
            'unique' => true,
            'name' => 'oauth_scopes_index_scope'
        ]);

        $this->createCollection('oauth_device');
        $this->createIndex('oauth_device', 'uuid', [
            'unique' => true,
            'name' => 'oauth_device_uuid_unique'
        ]);

        $this->createCollection('oauth_jwt');

    }

    public function down()
    {
        $this->dropCollection('oauth_clients');
        $this->dropCollection('oauth_access_tokens');
        $this->dropCollection('oauth_authorization_codes');
        $this->dropCollection('oauth_refresh_tokens');
        $this->dropCollection('oauth_scopes');
        $this->dropCollection('oauth_jwt');
        $this->dropCollection('oauth_device');

        return true;
    }
}
