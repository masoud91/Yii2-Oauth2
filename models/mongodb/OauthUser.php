<?php
namespace infinitydesign\idcoauth\models\mongodb;

use Yii;
use common\models\User;
use OAuth2\Storage\UserCredentialsInterface;

/**
 * This is the model class for collection "user".
 *
 */
class OauthUser extends User implements UserCredentialsInterface
{

    /**
     * Implemented for Oauth2 Interface
     *
     * @param $alias
     * @param $password
     * @return bool
     */
    public function checkUserCredentials($alias, $password)
    {

        Yii::warning("checkUserCredentials called");

        /** @var User $user */
        if( !$user = static::findUserByIdentifier($alias) ) {
            Yii::error("User::findUserByIdentifier() not implemented");
            return false;
        }

        return $user->validatePassword($password);
    }

    /**
     * Implemented for Oauth2 Interface
     *
     * @param string $username
     * @return array
     */
    public function getUserDetails($username)
    {
        Yii::warning("getUserDetails called");

        $user = static::findByUsername($username);
        return [
            'user_id' => (string) $user->_id,
        ];
    }

}
