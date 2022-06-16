<?php
/**
 * Created by PhpStorm.
 * User: eldor
 * Date: 18.09.2018
 * Time: 16:35
 */

namespace app\controllers;

use app\models\bank\BankPreferencesEditor;
use app\models\db\DbPreferencesEditor;
use app\models\db\DbRestoreModel;
use app\models\email\MailPreferencesEditor;
use app\models\management\BasePreferencesEditor;
use JetBrains\PhpStorm\ArrayShape;
use yii\filters\AccessControl;
use yii\web\Controller;

class ManagementController extends Controller
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
                        'actions' => ['index'],
                        'roles' => ['writer'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $basePreferencesEditor = new BasePreferencesEditor();
        $mailPreferencesEditor = new MailPreferencesEditor();
        $dbPreferencesEditor = new DbPreferencesEditor();
        $bankPreferencesEditor = new BankPreferencesEditor();
        $dbRestoreModel = new DbRestoreModel();
        return $this->render('index', [
            'basePreferencesEditor' => $basePreferencesEditor,
            'mailPreferencesEditor' => $mailPreferencesEditor,
            'bankPreferencesEditor' => $bankPreferencesEditor,
            'dbPreferencesEditor' => $dbPreferencesEditor,
            'dbRestoreModel' => $dbRestoreModel
        ]);
    }
}