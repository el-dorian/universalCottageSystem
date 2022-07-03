<?php


namespace app\models\databases;

use app\models\exceptions\DbSettingsException;
use app\models\utils\DbTransaction;
use app\models\utils\GrammarHandler;
use JetBrains\PhpStorm\ArrayShape;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @package app\models\databases
 *
 * @property int $id [int(10) unsigned]
 * @property string $personals [varchar(255)]
 * @property int $cottage [int(10) unsigned]
 * @property string $address [varchar(500)]
 * @property string $passport_data [varchar(500)]
 * @property bool $is_payer [tinyint(1)]
 * @property int $ownership_share [int(10) unsigned]
 * @property string $description
 */
class DbGardener extends ActiveRecord
{
    public mixed $emails = [];
    public mixed $phones = [];
    public ?string $test = null;

    public static function tableName(): string
    {
        return 'gardeners';
    }

    #[ArrayShape(['personals' => "string", 'address' => "string", 'passport_data' => "string", 'is_payer' => "string", 'ownership_share' => "string", 'emails' => "string", 'phones' => "string", 'description' => "string"])] public function attributeLabels(): array
    {
        return [
            'personals' => 'ФИО',
            'address' => 'Почтовый адрес',
            'passport_data' => 'Паспортные данные',
            'is_payer' => 'Владелец участка',
            'ownership_share' => 'Доля собственности',
            'emails' => 'Адреса электронной почты',
            'phones' => 'Номера телефонов',
            'description' => 'Комментарий',
        ];
    }

    public function rules(): array
    {
        return [
            [['personals', 'cottage'], 'required'],
            ['emails', 'validateEmails', 'skipOnEmpty' => true],
            ['phones', 'validatePhones', 'skipOnEmpty' => true],
            ['passport_data', 'string', 'skipOnEmpty' => true],
            ['description', 'string', 'skipOnEmpty' => true],
            ['ownership_share', 'required',
                'when' => function ($model) {
                    return $model->is_payer;
                },
                'whenClient' => "function(attribute, value) {
                      return $('#" . Html::getInputId($this, 'is_payer') . "').prop('checked');
                  }"
            ],
            [['address', 'passport_data'], 'string', 'max' => '500', 'skipOnEmpty' => true],
            [['is_payer'], 'required',
                'whenClient' => 'function(attribute,value){
                       if($("#' . Html::getInputId($this, 'is_payer') . '").prop("checked")){
                          $(".field-' . Html::getInputId($this, 'ownership_share') . '").show();
                       }else{
                          $(".field-' . Html::getInputId($this, 'ownership_share') . '").hide();
                       }
                       return true;
                  }'],
        ];
    }

    public function validateEmails($attribute): void
    {
        $data = $this->$attribute;
        if (is_string($data)) {
            $data = Json::encode($data);
        }
        foreach ($data as $key => $value) {
            foreach ($value as $attributeName => $attributeValue) {
                if ($attributeName === 'email') {
                    $target = "{$attribute}[$key][email]";
                    if (!filter_var($attributeValue, FILTER_VALIDATE_EMAIL)) {
                        $this->addError($target, 'Неверный адрес почты');
                    }
                }
            }
        }
    }

    public function validatePhones($attribute): void
    {
        $data = $this->$attribute;
        if (is_string($data)) {
            $data = Json::encode($data);
        }
        foreach ($data as $key => $value) {
            foreach ($value as $attributeName => $attributeValue) {
                if ($attributeName === 'phone') {
                    $target = "{$attribute}[$key][phone]";
                    if (!GrammarHandler::isValidPhone($attributeValue)) {
                        $this->addError($target, 'Неверный адрес почты');
                    }
                }
            }
        }
    }

    /**
     * @throws DbSettingsException
     */
    public function add(): void
    {
        $dbTransaction = new DbTransaction();
        // check that cottage exists
        $cottage = DbCottage::findOne($this->cottage);
        if ($cottage !== null) {
            $this->save();
            // add emails and phone numbers
            if (!empty($this->emails)) {
                if (is_string($this->emails)) {
                    $this->emails = Json::encode($this->emails);
                }
                foreach ($this->emails as $mailItem) {
                    $newEmail = new DbContactEmail();
                    $newEmail->cottage = $cottage->id;
                    $newEmail->gardener = $this->id;
                    $newEmail->address = $mailItem['email'];
                    $newEmail->description = $mailItem['description'];
                    if (!DbContactEmail::isDouble($newEmail)) {
                        $newEmail->save();
                    }
                }
            }
            if (!empty($this->phones)) {
                if (is_string($this->phones)) {
                    $this->phones = Json::encode($this->phones);
                }
                foreach ($this->phones as $phoneItem) {
                    $newPhone = new DbContactPhone();
                    $newPhone->cottage = $cottage->id;
                    $newPhone->gardener = $this->id;
                    $newPhone->number = GrammarHandler::clearPhoneNumber($phoneItem['phone']);
                    $newPhone->description = $phoneItem['description'];
                    if (!DbContactPhone::isDouble($newPhone)) {
                        $newPhone->save();
                    }
                }
            }
        }
        $dbTransaction->commitTransaction();
    }

    /**
     * @throws DbSettingsException
     * @throws \yii\db\StaleObjectException
     */
    public function change(): void
    {
        $dbTransaction = new DbTransaction();
        if (!empty($this->emails)) {
            if (is_string($this->emails)) {
                $this->emails = Json::encode($this->emails);
            }
            $existentEmails = DbContactEmail::findAll(['gardener' => $this->id]);

            $newEmails = [];
            // найду данные об имеющихся контактах
            foreach ($this->emails as $mailItem) {
                $newEmail = new DbContactEmail();
                $newEmail->cottage = $this->cottage;
                $newEmail->gardener = $this->id;
                $newEmail->address = $mailItem['email'];
                $newEmail->description = $mailItem['description'];
                // проверю на дубляж
                $isDouble = false;
                if (!empty($newEmails)) {
                    foreach ($newEmails as $newEmailItem) {
                        if ($newEmailItem->address === $newEmail->address) {
                            $isDouble = true;
                            break;
                        }
                    }
                }
                if (!$isDouble) {
                    $newEmails[] = $newEmail;
                }
            }
            if (count($existentEmails) === count($newEmails)) {
                // сохраню новые контакты вместо старых
                foreach ($existentEmails as $index => $value) {
                    $value->address = $newEmails[$index]->address;
                    $value->description = $newEmails[$index]->description;
                    $value->save();
                }
            } else if (count($existentEmails) < count($newEmails)) {
                // Перезапишу старые контакты и добавлю необходимое количество записей
                $difference = count($newEmails) - count($existentEmails);
                $handledCounter = 0;
                foreach ($existentEmails as $index => $existentEmail) {
                    $existentEmail->address = $newEmails[$index]->address;
                    $existentEmail->description = $newEmails[$index]->description;
                    $existentEmail->save();
                    $handledCounter++;
                }
                // теперь, пока не вычерпаем лимит-буду добавлять адреса
                while ($difference > 0) {
                    $newEmail = new DbContactEmail();
                    $newEmail->cottage = $this->cottage;
                    $newEmail->gardener = $this->id;
                    $newEmail->address = $newEmails[$handledCounter]->address;
                    $newEmail->description = $newEmails[$handledCounter]->description;
                    $newEmail->save();
                    $handledCounter++;
                    --$difference;
                }
            } else if (count($existentEmails) > count($newEmails)) {
                // изменю данные в существующих и удалю лишние
                $required = count($newEmails) - 1;
                foreach ($existentEmails as $index => $existentEmail) {
                    if ($index > $required) {
                        $existentEmail->delete();
                    } else {
                        $existentEmail->address = $newEmails[$index]->address;
                        $existentEmail->description = $newEmails[$index]->description;
                        $existentEmail->save();
                    }
                }
            }
        } else {
            // удалю имеющиеся адреса при наличии
            $existentEmails = DbContactEmail::findAll(['gardener' => $this->id]);
            if (!empty($existentEmails)) {
                foreach ($existentEmails as $existentEmail) {
                    $existentEmail->delete();
                }
            }
        }
        if (!empty($this->phones)) {
            if (is_string($this->phones)) {
                $this->phones = Json::encode($this->phones);
            }
            $existentPhones = DbContactPhone::findAll(['gardener' => $this->id]);

            $newPhones = [];
            // найду данные об имеющихся контактах
            foreach ($this->phones as $phoneItem) {
                $newPhone = new DbContactPhone();
                $newPhone->cottage = $this->cottage;
                $newPhone->gardener = $this->id;
                $newPhone->number =  GrammarHandler::clearPhoneNumber($phoneItem['phone']);
                $newPhone->description = $phoneItem['description'];
                // проверю на дубляж
                $isDouble = false;
                if (!empty($newPhones)) {
                    foreach ($newPhones as $newPhoneItem) {
                        if ($newPhoneItem->number === $newPhone->number) {
                            $isDouble = true;
                            break;
                        }
                    }
                }
                if (!$isDouble) {
                    $newPhones[] = $newPhone;
                }
            }
            if (count($existentPhones) === count($newPhones)) {
                // сохраню новые контакты вместо старых
                foreach ($existentPhones as $index => $value) {
                    $value->number = GrammarHandler::clearPhoneNumber($newPhones[$index]->number);
                    $value->description = $newPhones[$index]->description;
                    $value->save();
                }
            }
            else if (count($existentPhones) < count($newPhones)) {
                // Перезапишу старые контакты и добавлю необходимое количество записей
                $difference = count($newPhones) - count($existentPhones);
                $handledCounter = 0;
                foreach ($existentPhones as $index => $existentPhone) {
                    $existentPhone->number = GrammarHandler::clearPhoneNumber($newPhones[$index]->number);
                    $existentPhone->description = $newPhones[$index]->description;
                    $existentPhone->save();
                    $handledCounter++;
                }
                // теперь, пока не вычерпаем лимит-буду добавлять адреса
                while ($difference > 0) {
                    $newPhone = new DbContactPhone();
                    $newPhone->cottage = $this->cottage;
                    $newPhone->gardener = $this->id;
                    $newPhone->number = GrammarHandler::clearPhoneNumber($newPhones[$handledCounter]->number);
                    $newPhone->description = $newPhones[$handledCounter]->description;
                    $newPhone->save();
                    $handledCounter++;
                    --$difference;
                }
            } else if (count($existentPhones) > count($newPhones)) {
                // изменю данные в существующих и удалю лишние
                $required = count($newPhones) - 1;
                foreach ($existentPhones as $index => $existentPhone) {
                    if ($index > $required) {
                        $existentPhone->delete();
                    } else {
                        $existentPhone->number = GrammarHandler::clearPhoneNumber($newPhones[$index]->number);
                        $existentPhone->description = $newPhones[$index]->description;
                        $existentPhone->save();
                    }
                }
            }
        }
        else {
            // удалю имеющиеся адреса при наличии
            $existentPhones = DbContactPhone::findAll(['gardener' => $this->id]);
            if (!empty($existentPhones)) {
                foreach ($existentPhones as $existentPhone) {
                    $existentPhone->delete();
                }
            }
        }
        $this->save();
        $dbTransaction->commitTransaction();
    }
}