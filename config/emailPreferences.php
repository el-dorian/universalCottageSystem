<?php


use app\models\email\MailPreferences;

require_once __DIR__ . '/../models/email/MailPreferences.php';

return MailPreferences::getInstance();