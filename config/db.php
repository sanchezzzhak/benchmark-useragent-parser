<?php

return is_file(__DIR__ . '/local/db.php')
    ? require __DIR__ . '/local/db.php'
    : [
        'class' => 'yii\db\Connection',
        'dsn' => 'sqlite:' . dirname(__DIR__, 1) . '/runtime/data.db',
//    'username' => 'root',
//    'password' => '',
        'charset' => 'utf8',

        // Schema cache options (for production environment)
        //'enableSchemaCache' => true,
        //'schemaCacheDuration' => 60,
        //'schemaCache' => 'cache',
    ];
