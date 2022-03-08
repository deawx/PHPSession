<?php
/**
 * Session.php
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

use PHPSession\Interfaces\DriverInterface;

use function time;

class Session extends Base implements DriverInterface
{

    public function set(string $key, $value, ?int $ttl = null): self
    {
        $this->append($key, $value, $ttl);
        $this->save();
        return $this;
    }

    public function save()
    {
        $this->updated = false;
        $_SESSION[$this->name] = $this->data;
    }

    public function import()
    {
        $time = time();
        $data = $_SESSION[$this->name] ?? [];
        foreach ($data as $key => $value) {
            $ttl = $value['ttl'] ?? null;
            if($ttl !== null && $ttl < $time){
                continue;
            }
            $this->append($key, $value, $ttl);
        }
    }

    public function destroy()
    {
        $this->close();
        unset($_SESSION[$this->name]);
    }
}
