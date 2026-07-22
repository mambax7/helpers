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
use Xoops\Helpers\Service\HtmlSanitizer;

final class HtmlSanitizerTest extends TestCase
{
    protected function tearDown(): void
    {
        HtmlSanitizer::flush();
    }

    public function testIsAvailableReflectsHtmlPurifierPresence(): void
    {
        self::assertSame(
            class_exists(\HTMLPurifier::class) && class_exists(\HTMLPurifier_Config::class),
            HtmlSanitizer::isAvailable()
        );
    }

    public function testPurifyReturnsEmptyStringForEmptyInput(): void
    {
        // Short-circuits before HTMLPurifier is consulted, so this holds either way.
        self::assertSame('', HtmlSanitizer::purify(''));
    }

    public function testPurifyReturnsEmptyStringForWhitespaceOnlyInput(): void
    {
        self::assertSame('', HtmlSanitizer::purify("  \n\t "));
    }

    public function testPurifyReturnsNullWhenHtmlPurifierIsMissing(): void
    {
        if (HtmlSanitizer::isAvailable()) {
            self::markTestSkipped('HTMLPurifier is installed; the degradation path cannot be exercised.');
        }

        self::assertNull(HtmlSanitizer::purify('<p>hello</p>'));
    }

    public function testPurifierReturnsNullWhenHtmlPurifierIsMissing(): void
    {
        if (HtmlSanitizer::isAvailable()) {
            self::markTestSkipped('HTMLPurifier is installed; the degradation path cannot be exercised.');
        }

        self::assertNull(HtmlSanitizer::purifier());
    }

    public function testPurifyStripsDisallowedMarkup(): void
    {
        if (!HtmlSanitizer::isAvailable()) {
            self::markTestSkipped('HTMLPurifier is not installed.');
        }

        $result = HtmlSanitizer::purify('<p>safe</p><script>alert(1)</script>');

        self::assertIsString($result);
        self::assertStringContainsString('safe', $result);
        self::assertStringNotContainsString('<script', $result);
    }

    public function testPurifierIsMemoizedPerConfiguration(): void
    {
        if (!HtmlSanitizer::isAvailable()) {
            self::markTestSkipped('HTMLPurifier is not installed.');
        }

        self::assertSame(HtmlSanitizer::purifier(), HtmlSanitizer::purifier());
        self::assertNotSame(
            HtmlSanitizer::purifier(),
            HtmlSanitizer::purifier(['HTML.Allowed' => 'p'])
        );
    }

    public function testFlushDiscardsMemoizedInstances(): void
    {
        if (!HtmlSanitizer::isAvailable()) {
            self::markTestSkipped('HTMLPurifier is not installed.');
        }

        $first = HtmlSanitizer::purifier();
        HtmlSanitizer::flush();

        self::assertNotSame($first, HtmlSanitizer::purifier());
    }

    public function testDefaultAllowlistCoversCommonRichTextTagsAndExcludesScript(): void
    {
        self::assertStringContainsString('p,br,strong', HtmlSanitizer::DEFAULT_ALLOWED);
        self::assertStringContainsString('a[href|title|target|rel]', HtmlSanitizer::DEFAULT_ALLOWED);
        self::assertStringNotContainsString('script', HtmlSanitizer::DEFAULT_ALLOWED);
        self::assertStringNotContainsString('iframe', HtmlSanitizer::DEFAULT_ALLOWED);
    }
}
