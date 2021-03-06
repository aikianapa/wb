<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit10d8e36cc1cac79ce2653b63f64aad0d
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'Rct567\\' => 7,
        ),
        'N' => 
        array (
            'Nahid\\JsonQ\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Rct567\\' => 
        array (
            0 => __DIR__ . '/..' . '/rct567/dom-query/src/Rct567',
        ),
        'Nahid\\JsonQ\\' => 
        array (
            0 => __DIR__ . '/..' . '/nahid/jsonq/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PHPSQL' => 
            array (
                0 => __DIR__ . '/..' . '/soundintheory/php-sql-parser/src',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit10d8e36cc1cac79ce2653b63f64aad0d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit10d8e36cc1cac79ce2653b63f64aad0d::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit10d8e36cc1cac79ce2653b63f64aad0d::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
