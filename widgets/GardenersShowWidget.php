<?php

namespace app\widgets;

use app\models\databases\DbContactEmail;
use app\models\databases\DbContactPhone;
use app\models\databases\DbGardener;
use app\models\utils\GrammarHandler;
use yii\base\Widget;
use yii\helpers\Html;

class GardenersShowWidget extends Widget
{

    /**
     * @var DbGardener[]
     */
    public array $gardeners;
    public string $content = '';

    public function init():void
    {
        if(!empty($this->gardeners)){
            $this->content = '<table class="table"><thead><tr><th>ФИО</th><th>Адрес</th><th>E-mail</th><th>Телефон</th><th>Паспортные данные</th><th>Комментарий</th><th>Статус</th><th>Действия</th></tr></thead><tbody>';
            foreach ($this->gardeners as $gardener) {
                $status = $gardener->is_payer ? "Плательщик, доля собственности: $gardener->ownership_share" : 'Контактное лицо';

                // почта
                $mailAddresses = DbContactEmail::findAll(['gardener' => $gardener->id]);
                if(!empty($mailAddresses)){
                    $emails = '';
                    foreach ($mailAddresses as $mailAddress){
                        $emails .= "<a href='mailto:$mailAddress->address' title='$mailAddress->description' class='tooltip-enabled' data-toggle='tooltip' data-placement='top'>$mailAddress->address</a><br/>";
                    }
                }
                else{
                    $emails = '--';
                }
                // номера телефонов
                $phones = DbContactPhone::findAll(['gardener' => $gardener->id]);
                if(!empty($phones)){
                    $pn = '';
                    foreach ($phones as $phone){
                        $pn .= "<a href='tel:" . GrammarHandler::inflatePhoneNumber($phone->number) . "'  title='$phone->description' class='tooltip-enabled' data-toggle='tooltip' data-placement='top'>" . GrammarHandler::inflatePhoneNumber($phone->number) . "</a><br/>";
                    }
                }
                else{
                    $pn = '--';
                }

                $this->content .= "<tr><td>$gardener->personals</td><td>$gardener->address</td><td>$emails</td><td>$pn</td><td>$gardener->passport_data</td><td>$gardener->description</td><td>$status</td><td><div class='btn-group'><button class='btn btn-info ajax-form-trigger' data-action='/form/edit-gardener?id=$gardener->id'><i class='fa fa-edit'></i></button><button class='btn btn-danger ajax-promise-trigger' data-promise='Удалить данные?' data-action='/gardener/delete?id=$gardener->id'><i class='fa fa-trash'></i></button></div></td></tr>";

            }
            $this->content .= '</tbody></table>';
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