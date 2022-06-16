<?php


namespace app\models\handlers;

use app\models\databases\DbTelegramBinding;
use app\models\db\DbBackupModel;
use app\models\db\DbSettings;
use app\models\management\BasePreferences;
use CURLFile;
use DateTime;
use Exception;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\InvalidArgumentException;
use TelegramBot\Api\InvalidJsonException;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;
use Yii;

class TelegramHandler
{
    private static function getTgToken(): string
    {
        return BasePreferences::getInstance()->telegramApiKey;
    }

    public static function handleRequest(): void
    {
        try {
            /** @var BotApi|Client $bot */
            $bot = new Client(self::getTgToken());
// команда для start
            $bot->command(/**
             * @param $message Message
             */ 'start', static function ($message) use ($bot) {
                $answer = 'Добро пожаловать! /help для вывода команд';
                /** @var Message $message */
                $bot->sendMessage($message->getChat()->getId(), $answer);
            });

// команда для помощи
            $bot->command('help', static function ($message) use ($bot) {
                try {
                    /** @var Message $message */
                    // проверю, зарегистрирован ли пользователь как работающий у нас
                    if (DbTelegramBinding::contains($message->getChat()->getId())) {
                        $answer = 'Команды:
/help - вывод справки1';
                    } else {
                        $answer = 'Команды:
/help - вывод справки';
                    }
                    /** @var Message $message */
                    $bot->sendMessage($message->getChat()->getId(),
                        $answer,
                        null,
                        false,
                    );
                } catch (Exception $e) {
                    $bot->sendMessage($message->getChat()->getId(), $e->getMessage());
                }
            });

            $bot->command('test', static function ($message) {
                self::sendDebug("i here " . $message->getChat()->getId());
            });


            $bot->on(/**
             * @param $Update Update
             * @return string
             * @throws \TelegramBot\Api\Exception
             * @throws InvalidArgumentException
             */
                static function (Update $Update) use ($bot) {
                    $message = $Update->getMessage();
                    $bot->sendMessage($message->getChat()->getId(), self::handleSimpleText($message->getText(), $message));
                    return '';
                }, static function () {
                return true;
            });


            try {
                $bot->run();
            } catch (InvalidJsonException) {
                // что-то сделаю потом
            }
        } catch (Exception $e) {
            // запишу ошибку в лог
            $file = dirname($_SERVER['DOCUMENT_ROOT'] . './/') . '/logs/telebot_err_' . time() . '.log';
            $report = $e->getMessage();
            file_put_contents($file, $report);
        }
    }

    private static function handleSimpleText(string $msg_text, Message $message): string
    {
        if (str_starts_with($msg_text, "sql")) {
            self::sendDebug("request SQL");
            if (DbTelegramBinding::contains($message->getChat()->getId())) {
                $dbPreferencesInstance = DbSettings::getInstance();
                // handle mysql command
                exec($dbPreferencesInstance->mySqlPath . ' --user=' . $dbPreferencesInstance->dbLogin . ' --password=' . $dbPreferencesInstance->dbPass . ' --execute="' . mb_substr($msg_text, 3) . '"', $result);
                return serialize($result);
            }
        }
        if ($msg_text === BasePreferences::getInstance()->telegramSecret) {
            // регистрирую получателя
            DbTelegramBinding::register($message->getChat()->getId());
            return 'Ага, принято :) /help для списка команд';
        }
        return 'Не понимаю, о чём вы (вы написали ' . $msg_text . ')';
    }


    /**
     * @param string $errorInfo
     */
    public static function sendDebug(string $errorInfo): void
    {
        try {
            // проверю, есть ли учётные записи для отправки данных
            $subscribers = DbTelegramBinding::findAll(['log_level' => 1]);
            if (!empty($subscribers)) {
                /** @var BotApi|Client $bot */
                $bot = new Client(self::getTgToken());
                foreach ($subscribers as $subscriber) {
                    $bot->sendMessage($subscriber->person_id, $errorInfo);
                }
            }
        } catch (Exception $e) {
        }
    }

    public static function sendDebugFile(string $path, string $name, string $mime): void
    {
        if (is_file($path)) {
            $file = new CURLFile($path, $mime, $name);
            try {
                // проверю, есть ли учётные записи для отправки данных
                $subscribers = DbTelegramBinding::findAll(['log_level' => 1]);
                if (!empty($subscribers)) {
                    /** @var BotApi|Client $bot */
                    $bot = new Client(self::getTgToken());
                    foreach ($subscribers as $subscriber) {
                        $bot->sendDocument(
                            $subscriber->person_id,
                            $file
                        );
                    }
                }
            } catch (Exception) {

            }
        }
    }

    public static function sendDatabaseReserveCopy()
    {
        // make backup
        (new DbBackupModel())->backup();
        $date = new DateTime();
        $d = $date->format('Y-m-d H:i:s');
        self::sendDebugFile(Yii::$app->basePath . '/storage/db.sql', "Резервная копия базы данных " . BasePreferences::getInstance()->sntName . "  $d.sql", 'application/sql');

    }
}