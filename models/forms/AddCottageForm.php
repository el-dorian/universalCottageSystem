<?php

namespace app\models\forms;

use app\models\databases\DbCottage;
use app\models\databases\DbTariffElectricity;
use app\models\databases\DbTariffMembership;
use app\models\databases\DbTariffTarget;
use app\models\exceptions\MyException;
use app\models\handlers\TimeHandler;
use app\models\management\BasePreferences;
use app\models\utils\DbTransaction;
use yii\base\Model;
use yii\helpers\Html;

class AddCottageForm extends Model
{
    public string $cottageAlias = '';
    public int $cottageSquare = 0;
    public string $currentElectricityMeterValue = '0';
    public bool $isPayElectricity = true;
    public bool $isPayMembership = true;
    public bool $isPayTarget = true;
    public string $electricityPayedFor = '';
    public string $membershipPayedFor = '';
    public string $targetPayedFor = '';
    public string $registrationData = '';
    public string $cottageComment = '';
    public string $initialDeposit = '0';
    public bool $isSlave = false;
    public string $masterCottageName = '';

    public function attributeLabels(): array
    {
        return [
            'cottageAlias' => 'Номер участка',
            'cottageSquare' => 'Площадь участка',
            'isPayElectricity' => 'Оплата электроэнергии',
            'currentElectricityMeterValue' => 'Текущие показания счётчика',
            'electricityPayedFor' => 'Электроэнергия: последний оплаченный месяц',
            'isPayMembership' => 'Оплата членских взносов',
            'isPayTarget' => 'Оплата целевых взносов',
            'registrationData' => 'Данные собственности по участку',
            'cottageComment' => 'Комментарий для внутреннего пользования',
            'membershipPayedFor' => 'Членские взносы: последний оплаченный период',
            'targetPayedFor' => 'Целевые взносы: последний оплаченный период',
            'initialDeposit' => 'Депозит участка на момент регистрации',
            'isSlave' => 'Участок является дополнительным',
            'masterCottageName' => 'Номер главного участка',
        ];
    }

    public function rules(): array
    {
        return [
            // _mark POWER
            [['isPayElectricity'], 'required',
                'whenClient' => 'function(attribute,value){
                       if($("#' . Html::getInputId($this, 'isPayElectricity') . '").prop("checked")){
                          $(".field-' . Html::getInputId($this, 'currentElectricityMeterValue') . '").show();
                          $(".field-' . Html::getInputId($this, 'electricityPayedFor') . '").show();
                       }else{
                          $(".field-' . Html::getInputId($this, 'currentElectricityMeterValue') . '").hide();
                          $(".field-' . Html::getInputId($this, 'electricityPayedFor') . '").hide();
                       }
                       return true;
                  }'],
            ['currentElectricityMeterValue', 'required',
                'when' => function ($model) {
                    return $model->isPayElectricity;
                },
                'whenClient' => "function(attribute, value) {
                      return $('#" . Html::getInputId($this, 'isPayElectricity') . "').prop('checked');
                  }"
            ],
            [['currentElectricityMeterValue'], 'number', 'min' => 0],
            [['initialDeposit'], 'number', 'min' => 0],
            [['registrationData'], 'string', 'skipOnEmpty' => true],
            [['cottageComment'], 'string', 'skipOnEmpty' => true],
            ['electricityPayedFor', 'required',
                'when' => function ($model) {
                    return $model->isPayElectricity;
                },
                'whenClient' => "function(attribute, value) {
                      return $('#" . Html::getInputId($this, 'isPayElectricity') . "').prop('checked');
                  }"
            ],
            ['electricityPayedFor', 'validateElectricityMonth',
                'when' => function ($model) {
                    return $model->isPayElectricity;
                },
                'whenClient' => "function(attribute, value) {
                      return $('#" . Html::getInputId($this, 'isPayElectricity') . "').prop('checked');
                  }"
            ],
            // _mark MEMBERSHIP
            [['isPayMembership'], 'required',
                'whenClient' => 'function(attribute,value){
                       if($("#' . Html::getInputId($this, 'isPayMembership') . '").prop("checked")){
                          $(".field-' . Html::getInputId($this, 'membershipPayedFor') . '").show();
                       }else{
                          $(".field-' . Html::getInputId($this, 'membershipPayedFor') . '").hide();
                       }
                       return true;
                  }'],
            ['membershipPayedFor', 'required',
                'when' => function ($model) {
                    return $model->isPayMembership;
                },
                'whenClient' => "function(attribute, value) {
                      return $('#" . Html::getInputId($this, 'isPayMembership') . "').prop('checked');
                  }"
            ],
            ['membershipPayedFor', 'validateMembershipPayedFor',
                'when' => function ($model) {
                    return $model->isPayMembership;
                },
                'whenClient' => "function(attribute, value) {
                      return $('#" . Html::getInputId($this, 'isPayMembership') . "').prop('checked');
                  }"
            ],
            // _mark TARGET
            [['isPayTarget'], 'required',
                'whenClient' => 'function(attribute,value){
                       if($("#' . Html::getInputId($this, 'isPayTarget') . '").prop("checked")){
                          $(".field-' . Html::getInputId($this, 'targetPayedFor') . '").show();
                       }else{
                          $(".field-' . Html::getInputId($this, 'targetPayedFor') . '").hide();
                       }
                       return true;
                  }'],
            ['targetPayedFor', 'required',
                'when' => function ($model) {
                    return $model->isPayTarget;
                },
                'whenClient' => "function(attribute, value) {
                      return $('#" . Html::getInputId($this, 'isPayTarget') . "').prop('checked');
                  }"
            ],
            ['targetPayedFor', 'validateTargetPayedFor',
                'when' => function ($model) {
                    return $model->isPayTarget;
                },
                'whenClient' => "function(attribute, value) {
                      return $('#" . Html::getInputId($this, 'isPayTarget') . "').prop('checked');
                  }"
            ],
            // _mark SLAVE BLOCK

            [['isSlave'], 'required',
                'whenClient' => 'function(attribute,value){
                       if($("#' . Html::getInputId($this, 'isSlave') . '").prop("checked")){
                          $(".field-' . Html::getInputId($this, 'masterCottageName') . '").show();
                       }else{
                          $(".field-' . Html::getInputId($this, 'masterCottageName') . '").hide();
                       }
                       return true;
                  }'],
            ['masterCottageName', 'required',
                'when' => function ($model) {
                    return $model->isSlave;
                },
                'whenClient' => "function(attribute, value) {
                      return $('#" . Html::getInputId($this, 'isSlave') . "').prop('checked');
                  }"
            ],
            ['masterCottageName', 'validateMasterCottageName',
                'when' => function ($model) {
                    return $model->isSlave;
                },
                'whenClient' => "function(attribute, value) {
                      return $('#" . Html::getInputId($this, 'isSlave') . "').prop('checked');
                  }"
            ],
        ];
    }

    public function validateCottageAlias($attribute): void
    {
        if (empty($this->$attribute)) {
            $this->addError($attribute, 'Необходимо заполнить данные');
            return;
        }
        if (DbCottage::find()->where(['alias' => $this->$attribute])->count() > 0) {
            $this->addError($attribute, 'Участок с таким именем уже зарегистрирован!');
        }
    }

    public function validateElectricityMonth($attribute): void
    {
        if (!TimeHandler::isMonth($this->$attribute)) {
            $this->addError($attribute, 'Введите месяц в формате xxxx-xx');
            return;
        }
        try {
            $monthsForFill = TimeHandler::getMonths($this->$attribute);
        } catch (MyException) {
            $this->addError($attribute, 'Введите месяц в формате xxxx-xx');
            return;
        }
        if (!empty($monthsForFill)) {
            $unfilledMonths = '';
            foreach ($monthsForFill as $item) {
                if (DbTariffElectricity::find()->where(['period' => $item->full])->count() < 1) {
                    $unfilledMonths .= "$item->full, ";
                }
            }
        }
        if (!empty($unfilledMonths)) {
            $this->addError($attribute, "Не заполнены тарифы! $unfilledMonths заполните их в окне тарифов.");
        }
    }

    public function validateMembershipPayedFor($attribute): void
    {
        if(BasePreferences::getInstance()->membershipPaymentType === BasePreferences::STATE_PAY_QUARTERLY){
            try {
                if ($this->$attribute === TimeHandler::getCurrentQuarter()->full) {
                    return;
                }
            } catch (MyException) {}
            if (!TimeHandler::isQuarter($this->$attribute)) {
                $this->addError($attribute, 'Введите квартал в формате xxxx-x');
                return;
            }
            try {
                $periodsForFill = TimeHandler::getQuarters(TimeHandler::getNextQuarter($this->$attribute)->full, TimeHandler::getCurrentQuarter()->full);
            } catch (MyException) {
                $this->addError($attribute, 'Введите квартал в формате xxxx-xx');
                return;
            }
        }
        else{
            if ($this->$attribute === TimeHandler::getCurrentYear()) {
                return;
            }
            if (!TimeHandler::isYear($this->$attribute)) {
                $this->addError($attribute, 'Введите квартал в формате xxxx-x');
                return;
            }
            $periodsForFill = TimeHandler::getYears($this->$attribute, TimeHandler::getCurrentYear() - 1);
        }
        if (!empty($periodsForFill)) {
            $unfilledPeriods = '';
            foreach ($periodsForFill as $item) {
                if(BasePreferences::getInstance()->membershipPaymentType === BasePreferences::STATE_PAY_QUARTERLY && $item === TimeHandler::getCurrentQuarter()->full){continue;}
                if(BasePreferences::getInstance()->membershipPaymentType === BasePreferences::STATE_PAY_YEARLY && $item === TimeHandler::getCurrentYear()){continue;}
                if (DbTariffMembership::find()->where(['period' => $item])->count() < 1) {
                    $unfilledPeriods .= "$item, ";
                }
            }
        }
        if (!empty($unfilledPeriods)) {
            $this->addError($attribute, "Не заполнены тарифы! $unfilledPeriods заполните их в окне тарифов.");
        }
    }
    public function validateTargetPayedFor($attribute): void
    {
        if(BasePreferences::getInstance()->targetPaymentType === BasePreferences::STATE_PAY_QUARTERLY){
            if (!TimeHandler::isQuarter($this->$attribute)) {
                $this->addError($attribute, 'Введите квартал в формате xxxx-x');
                return;
            }
            try {
                $periodsForFill = TimeHandler::getQuarters($this->$attribute, TimeHandler::getCurrentQuarter()->full);
            } catch (MyException) {
                $this->addError($attribute, 'Введите квартал в формате xxxx-xx');
                return;
            }
        }
        else{
            if (!TimeHandler::isYear($this->$attribute)) {
                $this->addError($attribute, 'Введите квартал в формате xxxx-x');
                return;
            }
            $periodsForFill = TimeHandler::getYears($this->$attribute, TimeHandler::getCurrentYear() - 1);
        }
        if (!empty($periodsForFill)) {
            $unfilledPeriods = '';
            foreach ($periodsForFill as $item) {
                if (DbTariffTarget::find()->where(['period' => $item])->count() < 1) {
                    $unfilledPeriods .= "$item, ";
                }
            }
        }
        if (!empty($unfilledPeriods)) {
            $this->addError($attribute, "Не заполнены тарифы! $unfilledPeriods заполните их в окне тарифов.");
        }
    }

    public function validateMasterCottageName($attribute): void
    {
        if ($this->isSlave) {
            if (DbCottage::find()->where(['alias' => $this->$attribute])->count() < 1) {
                $this->addError($attribute, 'Участок с таким именем не зарегистрирован!');
            }
        }
    }

    public function save()
    {
        // поехали сохранять
        $dbTransaction = new DbTransaction();
        $newCottage = new DbCottage();
        $newCottage->alias = $this->cottageAlias;

    }
}

