<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit73c26d3770cc43eec2a964dd4bc0e1df
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPRealizer\\WCCustomProductTabManager\\' => 37,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPRealizer\\WCCustomProductTabManager\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'WPRealizer\\WCCustomProductTabManager\\Admin\\WCPTM_Admin' => __DIR__ . '/../..' . '/includes/Admin/WCPTM_Admin.php',
        'WPRealizer\\WCCustomProductTabManager\\Admin\\WCPTM_ProductTabsGroups' => __DIR__ . '/../..' . '/includes/Admin/WCPTM_ProductTabsGroups.php',
        'WPRealizer\\WCCustomProductTabManager\\Install\\WCPTM_Installer' => __DIR__ . '/../..' . '/includes/Install/WCPTM_Installer.php',
        'WPRealizer\\WCCustomProductTabManager\\WCPTM_Ajax' => __DIR__ . '/../..' . '/includes/WCPTM_Ajax.php',
        'WPRealizer\\WCCustomProductTabManager\\WCPTM_Assets' => __DIR__ . '/../..' . '/includes/WCPTM_Assets.php',
        'WPRealizer\\WCCustomProductTabManager\\WCPTM_ProductTabs' => __DIR__ . '/../..' . '/includes/WCPTM_ProductTabs.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit73c26d3770cc43eec2a964dd4bc0e1df::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit73c26d3770cc43eec2a964dd4bc0e1df::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit73c26d3770cc43eec2a964dd4bc0e1df::$classMap;

        }, null, ClassLoader::class);
    }
}
