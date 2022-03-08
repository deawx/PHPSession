<?php
/**
 * SegmentInterface.php
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

interface SegmentInterface
{

    /**
     * Oturum segmentinin adını döndürür.
     *
     * @return string|null
     */
    public function getSegmentName(): ?string;

    /**
     * Kütüphanenin versiyonunu döndürür.
     *
     * @return string
     */
    public function version(): string;

    /**
     * Kütüphaneyi Kapatır.
     *
     * @return void
     */
    public function close(): void;

    /**
     * Oturumu ve verilerini yok eder.
     *
     * @return void
     */
    public function destroy(): void;

    /**
     * Oturum verisini büyük/küçük harf duyarsız olarak bulur ve döndürür.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * Bir oturum verisi tanımlar.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     * @return SegmentInterface
     */
    public function set(string $key, $value, ?int $ttl = null): SegmentInterface;

    /**
     * Bir oturum verisini büyük/küçük karakter duyarsız varlığını kontrol eder.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Bir oturum verisini büyük/küçük harf duyarsız siler/kaldırır.
     *
     * @param string $key
     * @return SegmentInterface
     */
    public function remove(string $key): SegmentInterface;

    /**
     * @return array
     */
    public function all(): array;

}
