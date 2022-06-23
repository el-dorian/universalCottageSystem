<?php

namespace app\models\utils;

use app\models\exceptions\DbSettingsException;
use Yii;
use yii\db\Exception;
use yii\db\Transaction;

class DbTransaction
{
    /**
     * @var Transaction|null
     */
    private ?Transaction $transaction;

    public function __construct()
    {
        $db = Yii::$app->db;
        $this->transaction = $db->beginTransaction();
    }

    /**
     * @throws DbSettingsException
     */
    public function commitTransaction(): void
    {
        try {
            $this->transaction->commit();
        } catch (Exception $e) {
            throw new DbSettingsException('Ошибка работы с базой данных: ' . $e->getMessage(), 2);
        }
    }

    /**
     *
     */
    public function rollbackTransaction(): void
    {
        $this->transaction->rollBack();
    }
}