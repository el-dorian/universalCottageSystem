<?php

$appConfigFile =  __DIR__ . '/global_preferences.ini';
if(!is_file($appConfigFile)){
    file_put_contents($appConfigFile, 'Unnamed');
}

$preferences = file_get_contents($appConfigFile);
$preferencesArray = explode("\n", $preferences);

return [
    'gardensName' => $preferencesArray[0]
];