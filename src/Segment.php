<?php
/**
 * Segment.php
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

namespace PHPSession;

use PHPParameterBag\Interfaces\ParameterBagInterface;
use PHPParameterBag\ParameterBag;
use PHPSession\Drivers\Cookie;
use PHPSession\Drivers\Session;
use PHPSession\Exceptions\PHPSessionException;
use PHPSession\Interfaces\DriverInterface;

use const CASE_LOWER;

use function in_array;
use function trim;
use function array_merge;
use function array_change_key_case;

class Segment implements Interfaces\SegmentInterface
{
    public const VERSION = '1.0';
    public const DRIVER_COOKIE = 1;
    public const DRIVER_SESSION = 2;

    private array $drivers = [
        self::DRIVER_COOKIE,
        self::DRIVER_SESSION,
    ];

    protected string $name;
    protected DriverInterface $driver;
    protected ParameterBagInterface $options;

    private array $defaultOptions = [
        'domain'    => null,
        'path'      => '/',
        'secure'    => false,
        'httponly'  => true,
        'samesite'  => 'Strict',
        'signalgo'  => 'sha256',
        'algo'      => 'aes-128-cbc',
        'key'       => null
    ];

    public function __construct(string $name, int $driver = self::DRIVER_COOKIE, array $options = [])
    {
        if(empty($options)){
            $options = $this->defaultOptions;
        }else{
            $options = array_merge($this->defaultOptions, array_change_key_case($options, CASE_LOWER));
        }
        $this->options = new ParameterBag($options);
        if(in_array($driver, $this->drivers, true) === FALSE){
            throw new PHPSessionException('The driver you want to use is not valid. Just "Segment::DRIVER_COOKIE" or "Segment::DRIVER_SESSION"');
        }

        $this->name = trim($name);

        switch ($driver) {
            case self::DRIVER_SESSION:
                $this->driver = new Session($this->name, $this->options);
                break;
            case self::DRIVER_COOKIE:
                $this->driver = new Cookie($this->name, $this->options);
                break;
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @inheritDoc
     */
    public function version(): string
    {
        return self::VERSION;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->options->close();
        $this->driver->close();
        unset($this->options);
        unset($this->driver);
    }

    /**
     * @inheritDoc
     */
    public function destroy(): void
    {
        $this->driver->destroy();
    }

    /**
     * @inheritDoc
     */
    public function getSegmentName(): ?string
    {
        return $this->name ?? null;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        return $this->driver->get($key, $default);
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value, ?int $ttl = null): self
    {
        $this->driver->set($key, $value, $ttl);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->driver->has($key);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): self
    {
        $this->driver->remove($key);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->driver->all();
    }

}
