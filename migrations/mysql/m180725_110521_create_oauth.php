<?php

use yii\db\Migration;

/**
 * Class m180725_110521_create_oauth
 */
class m180725_110521_create_oauth extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $clientId = $this->string(64)->notNull();
        $userId = $this->integer();
        $deviceId = $this->string(32)->notNull();
        $authorizationCode = $this->string(128)->notNull();
        $expires = $this->integer()->notNull();
        $scope = $this->string(1024);
        $redirectUri = $this->string(1024)->notNull();
        $dt = $this->timestamp()->null();
//        $grantTypes = $this->string(256);

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('oauth_clients', [
            'id' => $this->primaryKey(),
            'client_id' => $clientId->unique(),
            'client_secret' => $this->string(256)->notNull(),
            'redirect_uri' => $redirectUri,
            'grant_types' => $this->string(256)->notNull(),
            'scope' => $scope,
            'user_id' => $userId,

//            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'cdt' => $dt,
            'udt' => $dt,
        ], $tableOptions);


        $this->createTable('oauth_access_tokens', [
            'id' => $this->primaryKey(),
            'access_token' => $this->string(64)->notNull()->unique(),
            'client_id' => $clientId,
            'expires' => $expires,
            'scope' => $scope,
            'user_id' => $userId,
            'device_id' => $deviceId,

//            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'cdt' => $dt,
            'udt' => $dt,
        ], $tableOptions);


        $this->createTable('oauth_refresh_tokens', [
            'id' => $this->primaryKey(),
            'refresh_token' => $this->string(64)->notNull()->unique(),
            'client_id' => $clientId,
            'expires' => $expires,
            'scope' => $scope,
            'user_id' => $userId,
            'device_id' => $deviceId,

//            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'cdt' => $dt,
            'udt' => $dt,
        ], $tableOptions);


        $this->createTable('oauth_authorization_codes', [
            'id' => $this->primaryKey(),
            'authorization_code' => $this->string(64)->notNull()->unique(),
            'client_id' => $clientId,
            'user_id' => $userId->notNull(),
            'redirect_uri' => $redirectUri,
            'expires' => $expires,
            'scope' => $scope,
            'id_token' => $this->string(128),
            'code' => $this->string(128),
            'device_id' => $deviceId->notNull(),

//            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'cdt' => $dt,
            'udt' => $dt,
        ], $tableOptions);


        $this->createTable('oauth_scopes', [
            'id' => $this->primaryKey(),
            'scope' => $scope,
            'is_default' => $this->smallInteger(),

//            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'cdt' => $dt,
            'udt' => $dt,
        ], $tableOptions);


        $this->createTable('oauth_device', [
            'id' => $this->primaryKey(),
            'uuid' => $this->string('64')->unique(),
            'pid' => $this->string('256'),
            'user_id' => $userId->notNull(),
            'os' => $this->string('32'),
            'os_version' => $this->string('16'),
            'phone_model' => $this->string('64'),
            'app_version' => $this->string('16'),

            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'cdt' => $dt,
            'udt' => $dt,
        ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180725_110521_create_oauth cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180725_110521_create_oauth cannot be reverted.\n";

        return false;
    }
    */
}
