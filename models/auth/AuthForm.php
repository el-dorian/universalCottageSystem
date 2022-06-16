<?php

namespace app\models\auth;

use JetBrains\PhpStorm\ArrayShape;
use Yii;
use yii\base\Model;

/**
 *
 * @property User|null $user
 */
class AuthForm extends Model{

    public const SCENARIO_LOGIN = 'login';
    public const SCENARIO_SIGNUP = 'signup';
    public const SCENARIO_LOGOUT = 'logout';

    public ?string $name = null;
    public ?string $password = null;

    public ?string $pass = null;

    private ?User $_user = null;

    #[ArrayShape([self::SCENARIO_LOGIN => "string[]", self::SCENARIO_SIGNUP => "string[]", self::SCENARIO_LOGOUT => "array"])] public function scenarios(): array
    {
        return [
            self::SCENARIO_LOGIN => ['name', 'password'],
            self::SCENARIO_SIGNUP => ['name'],
            self::SCENARIO_LOGOUT => [],
        ];
    }

    #[ArrayShape(['name' => "string", 'password' => "string"])] public function attributeLabels(): array
    {
        return [
            'name' => 'Логин',
            'password' => 'Пароль'
        ];
    }

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            // username and password are both required
            [['name', 'password'], 'required'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['name', 'validateUniqueName', 'on' => self::SCENARIO_SIGNUP],
            ['name', 'string', 'min' => 3, 'max' => 100],
            ['name', 'match', 'pattern' => '/^[a-z]*$/iu'],
            ['password', 'string', 'min' => 1,],
        ];
    }
    public function validateUniqueName($attribute): void
    {
        if (User::findByUsername($this->name)){
            $this->addError($attribute, 'Пользователь с таким именем уже существует.');
            Yii::$app->session->setFlash('error', 'Пользователь с таким именем уже существует.');
        }
    }
    public function validatePassword($attribute): void
    {
        if (!$this->hasErrors()){
            $user = $this->getUser();
            if($user !== null){
                if($user->failed_try > 5){
                    $this->addError($attribute, 'Слишком много попыток входа.');
                    Yii::$app->session->setFlash('error', 'Слишком много неудачных попыток входа. Повторите попытку через 5 минут.');
                }
                if (!$user || !$user->validatePassword($this->password)) {
                    ++$user->failed_try;
                    $this->addError($attribute, 'Неверный логин или пароль.');
                    Yii::$app->session->setFlash('error', 'Неверный логин или пароль.');
                }
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login(): bool
    {
        return Yii::$app->user->login($this->getUser());
    }
    /* 	public function signup(){
            $password = Yii::$app->getSecurity()->generateRandomString(10);
            $hash = Yii::$app->getSecurity()->generatePasswordHash($password);
            $auth_key = Yii::$app->getSecurity()->generateRandomString(32);
            $newUser = new User;
            $newUser->username = $this->name;
            $newUser->auth_key = $auth_key;
            $newUser->password_hash = $hash;
            $newUser->status = 1;
            if($newUser->save()){
                $this->pass = $password;
                // получаем id нового пользователя
                $id = User::findByUsername($this->name)->id;
                $auth = Yii::$app->authManager;
                $authorRole = $auth->getRole('reader');
                $auth->assign($authorRole, $id);
                return true;
            }
        } */

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->name);
        }
        return $this->_user;
    }
    /*public function permissions(){
            // Добавление роли =================================================
            $auth = Yii::$app->authManager;
            $managerRole = $auth->getRole('manager');
            $auth->assign($managerRole, 5);

            // добавляем разрешение "readSite"
            $read = $auth->createPermission('read');
            $read->description = 'Возможность чтения';
            $auth->add($read);

            // добавляем разрешение "redactSite"
            $write = $auth->createPermission('write');
            $write->description = 'Возможность редактирования';
            $auth->add($write);

            // добавляем разрешение "manageSite"
            $manage = $auth->createPermission('manage');
            $manage->description = 'Возможность управления';
            $auth->add($manage);


            // добавляем роль "author" и даём роли разрешение "createPost"
            $reader = $auth->createRole('reader');
            $reader->description = 'Учётная запись читателя';
            $auth->add($reader);
            $auth->addChild($reader, $read);
            // добавляем роль "author" и даём роли разрешение "createPost"
            $writer = $auth->createRole('writer');
            $writer->description = 'Учётная запись редактора';
            $auth->add($writer);
            $auth->addChild($writer, $write);
            $auth->addChild($writer, $reader);
            // добавляем роль "author" и даём роли разрешение "createPost"
            $manager = $auth->createRole('manager');
            $manager->description = 'Учётная запись администратора';
            $auth->add($manager);
            $auth->addChild($manager, $manage);
            $auth->addChild($manager, $writer);

            // Назначение ролей пользователям. 1 и 2 это IDs возвращаемые IdentityInterface::getId()
            // обычно реализуемый в модели User.
             $auth->assign($reader, $id);
            $auth->assign($writer, $id);
            $auth->assign($manager, $id);
    }*/
}