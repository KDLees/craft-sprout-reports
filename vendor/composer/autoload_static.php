<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9981dd2d8d7fdbc29b3016ba1ea344af
{
    public static $prefixLengthsPsr4 = array (
        'b' => 
        array (
            'barrelstrength\\sproutreports\\' => 29,
        ),
        'L' => 
        array (
            'League\\Csv\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'barrelstrength\\sproutreports\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'League\\Csv\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/csv/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9981dd2d8d7fdbc29b3016ba1ea344af::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9981dd2d8d7fdbc29b3016ba1ea344af::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}