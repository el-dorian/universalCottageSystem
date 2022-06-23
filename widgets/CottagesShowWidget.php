<?php

namespace app\widgets;

use app\models\management\BasePreferences;
use yii\base\Widget;
use yii\helpers\Html;

class CottagesShowWidget extends Widget
{

    /**
     * @var \app\models\databases\DbCottage[]
     */
    public array $cottages;
    public string $content = '';

    public function init()
    {
        $index = 1;
        $max = BasePreferences::getInstance()->cottagesQuantity;

        if($max > $index){
            if(!empty($this->cottages)){
/*                foreach ($this->cottages as $cottage) {
                    if ($cottage->cottageNumber === 0) {
                        continue;
                    }
                    while ($cottage->cottageNumber !== $index) {
                        $this->content .= "<div class='col-md-1 col-sm-2 col-xs-3 text-center margened inlined'><button class='btn empty cottage-button' data-index='$index' data-toggle='tooltip' data-placement='top' title='Регистрация участка № $index'>$index</button></div>";
                        $index++;
                    }
                    $additionalBlock = "<div class='col-xs-12 additional-block'>";
                    // проверю, есть ли почта у этого участка
                    if ($cottagesFastInfo->has_mail) {
                        $additionalBlock .= "<span class='custom-icon has-email'  data-toggle=\"tooltip\" data-placement=\"auto\" title=\"Есть адрес электронной почты\"></span>";
                    }
                    // проверю наличие незакрытого счёта у участка
                    if ($cottagesFastInfo->has_opened_bill) {
                        $unpayedBill = ComplexPayment::checkUnpayed($cottage->cottageNumber);
                        if ($unpayedBill !== null) {
                            $additionalBlock .= "<span class='custom-icon has-bill' data-toggle=\"tooltip\" data-placement=\"auto\" title=\"Есть открытый счёт\"></span>";
                            if ($unpayedBill->isInvoicePrinted) {
                                $additionalBlock .= "<span class='custom-icon invoice_printed' data-toggle=\"tooltip\" data-placement=\"auto\" title=\"Печаталась квитанция\"></span>";
                            }
                            if ($unpayedBill->isMessageSend) {
                                $additionalBlock .= "<span class='custom-icon message_sended' data-toggle=\"tooltip\" data-placement=\"auto\" title=\"Квитанция отправлена на электронную почту\"></span>";
                            }
                        }
                    }
                    $additionalBlock .= '</div>';
                    $additional = '';
                    if ($cottage->haveAdditional) {
                        $additional = "<span class='glyphicon glyphicon-plus'></span>";
                    }

                    if ($cottagesFastInfo->power_debt > 0 || $cottagesFastInfo->membership_debt > 0 || $cottagesFastInfo->target_debt > 0 || $cottagesFastInfo->single_debt > 0) {
                        $content = '';
                        if ($cottagesFastInfo->power_debt > 0) {
                            $content .= '<p>Электричество: <b class="text-danger">' . CashHandler::toSmoothRubles($cottagesFastInfo->power_debt) . '</b></p>';
                        }
                        if ($cottagesFastInfo->membership_debt > 0) {
                            $content .= '<p>Членские: <b class="text-danger">' . CashHandler::toSmoothRubles($cottagesFastInfo->membership_debt) . '</b></p>';
                        }
                        if ($cottagesFastInfo->target_debt > 0) {
                            $content .= '<p>Целевые: <b class="text-danger">' . CashHandler::toSmoothRubles($cottagesFastInfo->target_debt) . '</b></p>';
                        }
                        if ($cottagesFastInfo->single_debt > 0) {
                            $content .= '<p>Разовые: <b class="text-danger">' . CashHandler::toSmoothRubles($cottagesFastInfo->single_debt) . '</b></p>';
                        }

                        if ($cottage->deposit > 0) {
                            $deposit = CashHandler::toSmoothRubles($cottage->deposit);
                            $content .= "<p>Депозит участка: <b class=\"text-success\">{$deposit}</b></p>";
                        }
                        if ($cottagesFastInfo->expired) {
                            $color = 'btn-danger';
                        } else {
                            $color = 'btn-warning';
                        }
                        $this->content .= "<div class='col-md-1 col-sm-2 col-xs-3 text-center margened inlined'><a href='/show-cottage/$cottage->cottageNumber' class='btn $color popovered cottage-button' data-toggle='popover' data-placement='auto' data-title='Имеются задолженности' data-content='{$content}'>$cottage->cottageNumber {$additional}</a>$additionalBlock</div>";
                    } else {
                        $this->content .= "<div class='col-md-1 col-sm-2 col-xs-3 text-center margened inlined'><a href='/show-cottage/$cottage->cottageNumber' class='btn btn-success cottage-button'>$cottage->cottageNumber {$additional}</a>$additionalBlock</div>";
                    }
                    $index++;
                }
                while ($index <= $max) {
                    $this->content .= "<div class='col-md-1 col-sm-2 col-xs-3 text-center margened inlined'><button class='btn empty cottage-button' data-index='$index' data-toggle='tooltip' data-placement='top' title='Регистрация участка № $index'>$index</button></div>";
                    $index++;
                }*/
            }
            else{
                while ($index <= $max){
                    $this->content .= "<div class='col-md-1 col-sm-2 col-xs-3 text-center margened inlined'><button class='btn empty cottage-button' data-index='$index' data-toggle='tooltip' data-placement='top' title='Регистрация участка № $index'>$index</button></div>";
                    ++$index;
                }

            }
        }

    }

    /**
     * @return string
     */
    public function run():string
    {
        return Html::decode($this->content);
    }
}