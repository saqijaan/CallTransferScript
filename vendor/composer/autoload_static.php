<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit67b537dbddb070c9807e77a95efb5a74
{
    public static $files = array (
        'f81bdaac3a831a2a5e2bde721eaefd8e' => __DIR__ . '/../..' . '/config/functions.php',
        '29221454c2043d59929e1ecb6e369e72' => __DIR__ . '/../..' . '/config/variables.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Twilio\\' => 7,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Twilio\\' => 
        array (
            0 => __DIR__ . '/..' . '/twilio/sdk/Twilio',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit67b537dbddb070c9807e77a95efb5a74::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit67b537dbddb070c9807e77a95efb5a74::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}