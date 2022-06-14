<?php


namespace app\models\auth;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 *
 * @property mixed $authKey
 * @property mixed $id
 * @property mixed auth_key
 * @property mixed password_hash
 * @property string $username [varchar(255)]
 * @property string $password_reset_token [varchar(255)]
 * @property string $email [varchar(255)]
 * @property int $status [smallint(6)]
 * @property int $created_at [int(11)]
 * @property int $updated_at [int(11)]
 * @property string $signup_token [varchar(255)]
 * @property int $failed_try [smallint(6)]
 */
class User extends ActiveRecord implements IdentityInterface{

    public static function tableName(): string
    {
        return 'person';
    }


    /**
     * @inheritdoc
     */
    public static function findIdentity($id): User|IdentityInterface|null
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername(string $username): ?User
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey(){
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey): bool
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
