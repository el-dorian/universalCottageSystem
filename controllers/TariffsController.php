<?php
/**
 * Created by PhpStorm.
 * User: eldor
 * Date: 18.09.2018
 * Time: 16:35
 */

namespace app\controllers;

use app\models\databases\DbTariffElectricity;
use app\models\databases\DbTariffMembership;
use app\models\databases\DbTariffTarget;
use JetBrains\PhpStorm\ArrayShape;
use yii\filters\AccessControl;
use yii\web\Controller;

class TariffsController extends Controller
{
    /**
     * @return array
     */
    #[ArrayShape(['access' => "array"])] public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    return $this->redirect('/access-denied', 301);
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'index',
                        ],
                        'roles' => ['reader'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
       $existentTargetTariffs = DbTariffTarget::getAll();
       $existentMembershipTariffs = DbTariffMembership::getAll();
       $existentElectricityTariffs = DbTariffElectricity::getAll();
       return $this->render('index', ['targetTariffs' => $existentTargetTariffs, 'membershipTariffs' => $existentMembershipTariffs, 'electricityTariffs' => $existentElectricityTariffs]);
    }
}