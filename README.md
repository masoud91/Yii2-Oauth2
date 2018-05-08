Yii2 idco ouath2
================
idco ouath2 extension for Yii2

Requirements
------------
The extension needs following extensions to work with, you don't need install them manually

[Yii2-oauth-filsh](https://github.com/Filsh/yii2-oauth2-server)

```common\idco\mongodb\ActiveRecord```

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist infinitydesign/yii2-idcoauth "*"
```

or add

```
"infinitydesign/yii2-idcoauth": "*"
```

to the require section of your `composer.json` file.


Usage
-----
run the following migrations in order to create schemas and intial documents ( only supprot mongodb by now )
```
./yii mongodb-migrate --migrationPath=@vendor/infinitydesign/yii2-idcoauth/migrations
```

add the following config under the modules section:
```php
'oauth2' => [
    'class' => 'filsh\yii2\oauth2server\Module',
    'tokenParamName' => 'accessToken',
    'tokenAccessLifetime' => 3600 * 24,
    'storageMap' => [
        'user_credentials' => 'infinitydesign\idcoauth\OauthUser',
        'client' => 'infinitydesign\idcoauth\OauthClients',
        'access_token' => 'infinitydesign\idcoauth\OauthAccessTokens',
        'refresh_token' => 'infinitydesign\idcoauth\OauthRefreshTokens',
        'authorization_code' => 'infinitydesign\idcoauth\OauthAuthorizationCodes',
        'client_credentials' => 'infinitydesign\idcoauth\OauthClients',
        'scope' => 'infinitydesign\idcoauth\OauthScopes',
    ],
    'grantTypes' => [
        'user_credentials' => [
            'class' => 'infinitydesign\idcoauth\UserCredentials',
            'allow_public_clients' => false
        ],
        'refresh_token' => [
            'class' => 'infinitydesign\idcoauth\RefreshToken',
            'always_issue_new_refresh_token' => true
        ],
        'authorization_code' => [
            'class' => 'infinitydesign\idcoauth\AuthorizationCode',
        ],
    ],
    'components' => [
        'request' => function () {
            return \filsh\yii2\oauth2server\Request::createFromGlobals();
        },
        'response' => [
            'class' => \filsh\yii2\oauth2server\Response::class,
        ],
    ],
]
```

```php
<?= \infinitydesign\idcoauth\AutoloadExample::widget(); ?>
```
