<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit680babba573d620cf2ff226549fa4028
{
    public static $prefixLengthsPsr4 = array (
        'Y' => 
        array (
            'YourNamespace\\' => 14,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'M' => 
        array (
            'Monolog\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'YourNamespace\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Monolog\\' => 
        array (
            0 => __DIR__ . '/..' . '/monolog/monolog/src/Monolog',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit680babba573d620cf2ff226549fa4028::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit680babba573d620cf2ff226549fa4028::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit680babba573d620cf2ff226549fa4028::$classMap;

        }, null, ClassLoader::class);
    }
}