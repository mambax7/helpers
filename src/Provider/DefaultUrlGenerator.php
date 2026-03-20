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

namespace Xoops\Helpers\Provider;

use Xoops\Helpers\Contracts\UrlGeneratorInterface;

/**
 * Default URL generator using XOOPS constants.
 *
 * Uses XOOPS_URL as the base URL. Falls back to server
 * variables if XOOPS_URL is not defined.
 */
class DefaultUrlGenerator implements UrlGeneratorInterface
{
    public function generate(string $path = '', array $query = [], bool $secure = false): string
    {
        $base = $this->getBaseUrl($secure);
        $url = rtrim($base, '/');

        if ($path !== '') {
            $url .= '/' . ltrim($path, '/');
        }

        if ($query !== []) {
            $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        return $url;
    }

    public function asset(string $path, bool $secure = false): string
    {
        return $this->generate($path, [], $secure);
    }

    public function module(string $dirname, string $path = '', array $query = []): string
    {
        $modulePath = 'modules/' . $dirname;

        if ($path !== '') {
            $modulePath .= '/' . ltrim($path, '/');
        }

        return $this->generate($modulePath, $query);
    }

    public function theme(string $name, string $path = ''): string
    {
        $themePath = 'themes/' . $name;

        if ($path !== '') {
            $themePath .= '/' . ltrim($path, '/');
        }

        return $this->generate($themePath);
    }

    private function getBaseUrl(bool $secure): string
    {
        if (defined('XOOPS_URL')) {
            $url = XOOPS_URL;

            if ($secure) {
                return str_replace('http://', 'https://', $url);
            }

            return $url;
        }

        $scheme = $secure
            ? 'https'
            : ((($_SERVER['HTTPS'] ?? 'off') !== 'off') ? 'https' : 'http');

        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host;
    }
}
