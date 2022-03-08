<?php
/**
 * DriverInterface.php
 *
 * This file is part of PHPSession.
 *
 * @author     Muhammet ŞAFAK <info@muhammetsafak.com.tr>
 * @copyright  Copyright © 2022 PHPSession
 * @license    https://github.com/muhametsafak/PHPSession/blob/main/LICENSE  MIT
 * @version    1.0
 * @link       https://www.muhammetsafak.com.tr
 */

declare(strict_types=1);

namespace PHPSession\Interfaces;

interface DriverInterface
{

    public function close(): void;

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * @param string $key
     * @param $value
     * @param int|null $ttl
     * @return DriverInterface
     */
    public function set(string $key, $value, ?int $ttl = null): DriverInterface;

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @param string $key
     * @return DriverInterface
     */
    public function remove(string $key): DriverInterface;

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @return void
     */
    public function save();

    /**
     * @return void
     */
    public function import();

    /**
     * @return void
     */
    public function destroy();

}
