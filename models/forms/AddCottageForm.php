<?php

namespace app\models\forms;

use app\models\databases\DbCottage;
use app\models\databases\DbDepositTransfer;
use app\models\databases\DbElectricityMeter;
use app\models\databases\DbTariffElectricity;
use app\models\databases\DbTariffMembership;
use app\models\databases\DbTariffTarget;
use app\models\electricity\ElectricityAccrualsHandler;
use app\models\exceptions\DbSettingsException;
use app\models\exceptions\MyException;
use app\models\handlers\TelegramHandler;
use app\models\handlers\TimeHandler;
use app\models\management\BasePreferences;
use app\models\membership\MembershipAccrualsHandler;
use app\models\target\TargetAccrualsHandler;
use app\models\utils\CashHandler;
use app\models\utils\DbTransaction;
use app\validators\ElectricityMonthValidator;
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
            [['cottageAlias'], 'required'],
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
            ['electricityPayedFor', ElectricityMonthValidator::class,
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

    public function validateMembershipPayedFor($attribute): void
    {
        if (BasePreferences::getInstance()->membershipPaymentType === BasePreferences::STATE_PAY_QUARTERLY) {
            if ($this->$attribute === TimeHandler::getCurrentQuarter()->full) {
                return;
            }
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
        } else {
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
                if (BasePreferences::getInstance()->membershipPaymentType === BasePreferences::STATE_PAY_QUARTERLY && $item === TimeHandler::getCurrentQuarter()->full) {
                    continue;
                }
                if (BasePreferences::getInstance()->membershipPaymentType === BasePreferences::STATE_PAY_YEARLY && $item === TimeHandler::getCurrentYear()) {
                    continue;
                }
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
        if (BasePreferences::getInstance()->targetPaymentType === BasePreferences::STATE_PAY_QUARTERLY) {
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
        } else {
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
        if ($this->isSlave && DbCottage::find()->where(['alias' => $this->$attribute])->count() < 1) {
            $this->addError($attribute, 'Участок с таким именем не зарегистрирован!');
        }
    }

    /**
     * @throws MyException
     */
    public function save(): void
    {
        // поехали сохранять
        $dbTransaction = new DbTransaction();
        $newCottage = new DbCottage();
        $newCottage->square = $this->cottageSquare;
        $newCottage->alias = $this->cottageAlias;
        $newCottage->comment = $this->cottageComment;
        $newCottage->registration_information = $this->registrationData;
        $newCottage->is_pay_for_electricity = $this->isPayElectricity;
        $newCottage->is_pay_for_membership = $this->isPayMembership;
        $newCottage->is_pay_for_target = $this->isPayTarget;
        $newCottage->deposit = 0;
        $newCottage->save();
        // _mark handle deposit
        if ($this->initialDeposit > 0) {
            $depositTransfer = new DbDepositTransfer();
            $depositTransfer->cottage = $newCottage->id;
            $depositTransfer->direction = DbDepositTransfer::INCOMING;
            $depositTransfer->description = 'Депозит, зачисленный при регистрации участка';
            $depositTransfer->sum = CashHandler::floatSumToIntSum($this->initialDeposit);
            $depositTransfer->cottage_deposit_before = 0;
            $depositTransfer->cottage_deposit_after = $depositTransfer->sum;
            $depositTransfer->action_timestamp = time();
            $depositTransfer->save();
        }
        // _mark append membership accruals
        if ($newCottage->is_pay_for_membership) {
            $newCottage->debt_membership = MembershipAccrualsHandler::registerNewCottage($newCottage, $this->membershipPayedFor);
        }
        // _mark append target accruals
        if ($newCottage->is_pay_for_target) {
            $newCottage->debt_target = TargetAccrualsHandler::registerNewCottage($newCottage, $this->targetPayedFor);
        }
        if ($newCottage->is_pay_for_electricity) {
            // add new counter
            $newMeter = new DbElectricityMeter();
            $newMeter->cottage = $newCottage->id;
            $newMeter->indication = $this->currentElectricityMeterValue;
            $newMeter->save();
            // зарегистрирую месяц, в котором была произведена последняя оплата как последний зарегистрированный
            $newCottage->debt_electricity = 0;
            ElectricityAccrualsHandler::registerNewCounter($newMeter, $this->electricityPayedFor);
        }
        $newCottage->debt_single = 0;
        $newCottage->total_debt = $newCottage->debt_membership + $newCottage->debt_target;
        $newCottage->save();
        try {
            $dbTransaction->commitTransaction();
        } catch (DbSettingsException $e) {
            throw new MyException($e->getMessage());
        }
        TelegramHandler::sendDebug("Добавлен новый участок: $newCottage->alias");
    }
}

