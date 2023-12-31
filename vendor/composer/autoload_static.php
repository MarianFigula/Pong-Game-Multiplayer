<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6084def6c142fb423607f42e0ac8d0fd
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Workerman\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Workerman\\' => 
        array (
            0 => __DIR__ . '/..' . '/workerman/workerman',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6084def6c142fb423607f42e0ac8d0fd::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6084def6c142fb423607f42e0ac8d0fd::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6084def6c142fb423607f42e0ac8d0fd::$classMap;

        }, null, ClassLoader::class);
    }
}
