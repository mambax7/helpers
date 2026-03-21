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

namespace Xoops\Helpers\Utility;

/**
 * XSS-safe HTML construction helpers.
 *
 * Builds HTML attributes, CSS class lists, and tags with
 * automatic escaping. Eliminates the most common XSS vector
 * in XOOPS modules: manual HTML string concatenation.
 *
 * Usage:
 *   echo HtmlBuilder::attributes([
 *       'class' => 'btn btn-primary',
 *       'disabled' => true,        // renders as just "disabled"
 *       'data-id' => $userInput,   // auto-escaped
 *       'hidden' => false,         // omitted entirely
 *   ]);
 *   // class="btn btn-primary" disabled data-id="safe&amp;value"
 */
final class HtmlBuilder
{
    /**
     * Build an HTML attribute string from an associative array.
     *
     * Boolean true renders a valueless attribute (e.g. "disabled").
     * Boolean false or null omits the attribute entirely.
     * All string values are escaped via htmlspecialchars.
     *
     * @param array<string, string|bool|int|float|null> $attributes
     */
    public static function attributes(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $key => $value) {
            if ($value === false || $value === null) {
                continue;
            }

            if ($value === true) {
                $parts[] = self::escape($key);
                continue;
            }

            $parts[] = self::escape($key) . '="' . self::escape((string) $value) . '"';
        }

        return implode(' ', $parts);
    }

    /**
     * Build a conditional CSS class string.
     *
     * Accepts a mix of strings (always included) and
     * string => bool pairs (included when true).
     *
     * @param array<int|string, string|bool> $classes
     *
     * Usage:
     *   HtmlBuilder::classes([
     *       'btn',
     *       'btn-primary' => $isPrimary,
     *       'btn-lg' => $isLarge,
     *       'disabled' => false,
     *   ]);
     *   // "btn btn-primary" (if $isPrimary is true)
     */
    public static function classes(array $classes): string
    {
        $result = [];

        foreach ($classes as $class => $condition) {
            if (is_int($class)) {
                // Numeric key: $condition is the class name (always included)
                $result[] = self::escape((string) $condition);
            } elseif ($condition) {
                // String key: include class only if condition is truthy
                $result[] = self::escape($class);
            }
        }

        return implode(' ', $result);
    }

    /**
     * Build a complete HTML tag.
     *
     * @param string                                     $tag        Tag name (e.g. "div", "span", "input")
     * @param array<string, string|bool|int|float|null>  $attributes Tag attributes
     * @param string|null                                $content    Inner HTML (NOT escaped — pass pre-escaped content)
     * @param bool                                       $selfClose  Self-closing tag (e.g. <input />)
     */
    public static function tag(string $tag, array $attributes = [], ?string $content = null, bool $selfClose = false): string
    {
        $tag = self::escape($tag);
        $attrs = self::attributes($attributes);
        $attrStr = $attrs !== '' ? ' ' . $attrs : '';

        if ($selfClose) {
            return '<' . $tag . $attrStr . ' />';
        }

        return '<' . $tag . $attrStr . '>' . ($content ?? '') . '</' . $tag . '>';
    }

    /**
     * Escape a string for safe HTML output.
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Build a <link> tag for a stylesheet.
     *
     * @param array<string, string|bool|int|float|null> $attributes
     */
    public static function stylesheet(string $href, array $attributes = []): string
    {
        return self::tag('link', array_merge([
            'rel' => 'stylesheet',
            'href' => $href,
        ], $attributes), selfClose: true);
    }

    /**
     * Build a <script> tag.
     *
     * @param array<string, string|bool|int|float|null> $attributes
     */
    public static function script(string $src, array $attributes = []): string
    {
        return self::tag('script', array_merge([
            'src' => $src,
        ], $attributes), content: '');
    }

    /**
     * Build a <meta> tag.
     *
     * @param array<string, string> $attributes
     */
    public static function meta(array $attributes): string
    {
        return self::tag('meta', $attributes, selfClose: true);
    }
}
