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

namespace Xoops\Helpers\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Xoops\Helpers\Contracts\PathLocatorInterface;
use Xoops\Helpers\Service\Path;

final class PathTest extends TestCase
{
    protected function tearDown(): void
    {
        Path::reset();
    }

    public function testBaseReturnsRootPath(): void
    {
        self::assertSame(XOOPS_ROOT_PATH, Path::base());
    }

    public function testBaseWithRelativePath(): void
    {
        $expected = XOOPS_ROOT_PATH . DIRECTORY_SEPARATOR . 'includes';
        self::assertSame($expected, Path::base('includes'));
    }

    public function testStorageReturnsVarPath(): void
    {
        self::assertSame(XOOPS_VAR_PATH, Path::storage());
    }

    public function testUploadsReturnsUploadPath(): void
    {
        self::assertSame(XOOPS_UPLOAD_PATH, Path::uploads());
    }

    public function testModulePath(): void
    {
        $result = Path::module('news', 'class');
        self::assertStringContainsString('modules', $result);
        self::assertStringContainsString('news', $result);
        self::assertStringContainsString('class', $result);
    }

    public function testThemePath(): void
    {
        $result = Path::theme('starter', 'css/style.css');
        self::assertStringContainsString('themes', $result);
        self::assertStringContainsString('starter', $result);
    }

    public function testCustomLocator(): void
    {
        $mock = new class implements PathLocatorInterface {
            public function basePath(string $path = ''): string
            {
                return '/custom' . ($path ? '/' . $path : '');
            }

            public function publicPath(string $path = ''): string
            {
                return $this->basePath($path);
            }

            public function storagePath(string $path = ''): string
            {
                return '/custom/storage' . ($path ? '/' . $path : '');
            }

            public function uploadsPath(string $path = ''): string
            {
                return '/custom/uploads' . ($path ? '/' . $path : '');
            }

            public function modulesPath(string $path = ''): string
            {
                return '/custom/modules' . ($path ? '/' . $path : '');
            }

            public function themesPath(string $path = ''): string
            {
                return '/custom/themes' . ($path ? '/' . $path : '');
            }

            public function modulePath(string $dirname, string $path = ''): string
            {
                return '/custom/modules/' . $dirname . ($path ? '/' . $path : '');
            }

            public function themePath(string $name, string $path = ''): string
            {
                return '/custom/themes/' . $name . ($path ? '/' . $path : '');
            }
        };

        Path::use($mock);
        self::assertSame('/custom', Path::base());
        self::assertSame('/custom/storage', Path::storage());
    }
}
