<?php
/**
 * Base.php
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

namespace PHPSession\Drivers;

use PHPParameterBag\Interfaces\ParameterBagInterface;
use PHPSession\Interfaces\DriverInterface;

use function strtolower;
use function time;

abstract class Base implements \PHPSession\Interfaces\DriverInterface
{

    protected ParameterBagInterface $options;

    protected string $name;

    protected array $data = [];

    protected array $names = [];

    protected bool $updated = false;

    public function __construct(string $name, ParameterBagInterface &$options)
    {
        $this->name = $name;
        $this->options = $options;
        $this->import();
    }

    public function close(): void
    {
        if($this->updated === FALSE){
            $this->save();
            $this->updated = false;
        }
        unset($this->data);
        unset($this->names);
        unset($this->options);
        unset($this->name);
    }

    public function get(string $key, $default = null)
    {
        $lowercase = strtolower($key);
        $id = $this->names[$lowercase] ?? $key;
        return isset($this->data[$id]) ? $this->data[$id]['value'] : $default;
    }

    public function set(string $key, $value, ?int $ttl = null): self
    {
        $this->append($key, $value, ($ttl + time()));
        return $this;
    }

    protected function append(string $key, $value, ?int $ttl = null): void
    {
        $this->updated = true;
        $lowercase = strtolower($key);
        $id = $this->names[$lowercase] ?? $key;
        $this->data[$id] = [
            'value'     => $value,
            'ttl'       => $ttl,
        ];
        $this->names[$lowercase] = $id;
    }

    public function has(string $key): bool
    {
        $id = $this->named($key);
        return isset($this->data[$id]);
    }

    public function remove(string $key): self
    {
        $this->updated = true;
        $lowercase = strtolower($key);
        $id = $this->names[$lowercase] ?? $key;
        if(isset($this->data[$id])){
            unset($this->data[$id]);
            unset($this->names[$lowercase]);
        }
        return $this;
    }

    public function all(): array
    {
        $tmp = [];
        foreach ($this->data as $key => $value) {
            $tmp[$key] = $value['value'];
        }
        return $tmp;
    }

    abstract public function save();

    abstract public function import();

    abstract public function destroy();

    protected function named(string $key): string
    {
        $lowercase = strtolower($key);
        return $this->names[$lowercase] ?? $key;
    }

}
