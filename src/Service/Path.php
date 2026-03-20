<?php

declare(strict_types=1);

/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    2000-2026 XOOPS Project (https://xoops.org/)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team
 */

namespace Xoops\Helpers\Service;

use Xoops\Helpers\Contracts\PathLocatorInterface;
use Xoops\Helpers\Provider\DefaultPathLocator;

/**
 * Static facade for XOOPS path resolution.
 *
 * Zero-config: works out of the box using XOOPS constants.
 * Override via Path::use() for testing or custom installations.
 *
 * Usage:
 *   Path::base();                       // XOOPS_ROOT_PATH
 *   Path::storage('caches/xmf');        // XOOPS_VAR_PATH/caches/xmf
 *   Path::uploads('images/logo.png');   // XOOPS_UPLOAD_PATH/images/logo.png
 *   Path::module('news', 'class');      // XOOPS_ROOT_PATH/modules/news/class
 */
final class Path
{
    private static ?PathLocatorInterface $locator = null;

    /**
     * Inject a custom path locator (useful for testing).
     */
    public static function use(PathLocatorInterface $locator): void
    {
        self::$locator = $locator;
    }

    /**
     * Reset to the default locator.
     */
    public static function reset(): void
    {
        self::$locator = null;
    }

    public static function base(string $path = ''): string
    {
        return self::locator()->basePath($path);
    }

    public static function public(string $path = ''): string
    {
        return self::locator()->publicPath($path);
    }

    public static function storage(string $path = ''): string
    {
        return self::locator()->storagePath($path);
    }

    public static function uploads(string $path = ''): string
    {
        return self::locator()->uploadsPath($path);
    }

    public static function modules(string $path = ''): string
    {
        return self::locator()->modulesPath($path);
    }

    public static function themes(string $path = ''): string
    {
        return self::locator()->themesPath($path);
    }

    public static function module(string $dirname, string $path = ''): string
    {
        return self::locator()->modulePath($dirname, $path);
    }

    public static function theme(string $name, string $path = ''): string
    {
        return self::locator()->themePath($name, $path);
    }

    private static function locator(): PathLocatorInterface
    {
        return self::$locator ??= new DefaultPathLocator();
    }
}
