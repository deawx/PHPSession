<?php
/**
 * Cookie.php
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

use PHPSession\Exceptions\PHPSessionException;
use PHPSession\Interfaces\DriverInterface;

use function time;
use function setcookie;
use function base64_decode;
use function base64_encode;
use function json_decode;
use function json_encode;
use function strpos;
use function explode;
use function hash_hmac;
use function openssl_encrypt;
use function openssl_decrypt;
use function openssl_cipher_iv_length;

class Cookie extends Base implements DriverInterface
{

    private string $separator = '@_|_@';

    public function save()
    {
        $this->updated = false;
        $algo = $this->options->get('algo', null);
        if(empty($algo)){
            throw new PHPSessionException();
        }
        $ivLen = openssl_cipher_iv_length($algo);
        $iv = $this->generateIV($ivLen);
        $data = json_encode($this->data);
        $key = $this->options->get('key', null);
        if(empty($key)){
            throw new PHPSessionException();
        }
        if(($data = openssl_encrypt($data, $algo, $key, 0, $iv)) === FALSE){
            throw new PHPSessionException();
        }
        $data = json_encode(['IV' => $iv, 'data' => $data]);
        if(($signature = $this->signature($data)) === null){
            throw new PHPSessionException();
        }
        $data .= $this->separator . $signature;
        $data = base64_encode($data);

        $options = [
            'secure'    => $this->options->get('secure', false),
            'httponly'  => $this->options->get('httponly', true),
            'path'      => $this->options->get('path', '/'),
            'domain'    => $this->options->get('domain', null),
            'samesite'  => $this->options->get('sameSite', 'Strict')
        ];
        setcookie($this->name, $data, $options);
    }

    public function import()
    {
        $time = time();
        $data = $_COOKIE[$this->name] ?? null;
        if(empty($data)){
            return;
        }
        if(($data = base64_decode($data)) === FALSE){
            throw new PHPSessionException('The session(' . $this->name . ') cookie could not be read or is not in the correct format.');
        }
        if(strpos($data, $this->separator) === FALSE){
            throw new PHPSessionException('The session(' . $this->name . ') cookie could not be read or is not in the correct format.');
        }
        [$cookies, $signature] = explode($this->separator, $data, 2);
        if($signature !== $this->signature($cookies)){
            throw new PHPSessionException('Failed to verify signature of session (' . $this->name . ').');
        }

        $data = json_decode($cookies, true);
        if(!isset($data['data']) || !isset($data['IV'])){
            throw new PHPSessionException('Failed to retrieve session (' . $this->name . ') data.');
        }

        $algo = $this->options->get('algo', null);
        $key = $this->options->get('key', null);
        $iv = $data['IV'];
        if(empty($algo) || empty($key) || empty($iv)){
            throw new PHPSessionException('Requirements must be fully satisfied to parse session (' . $this->name . ') data.');
        }

        if(($data = openssl_decrypt($data['data'], $algo, $iv)) === FALSE){
            throw new PHPSessionException('Session (' . $this->name . ') data could not be resolved.');
        }
        $data = json_decode($data, true);
        foreach ($data as $key => $value) {
            $ttl = $value['ttl'] ?? null;
            if($ttl !== null && $ttl < $time){
                continue;
            }
            $this->append($key, $value, $ttl);
        }
    }

    protected function signature(string $data): ?string
    {
        $algo = $this->options->get('signAlgo', null);
        $key = $this->options->get('key', null);
        if($algo === null || $key === null){
            throw new PHPSessionException('The session (' . $this->name . ') cookie could not be signed. Key and Algorithm must be defined.');
        }
        if(($signature = hash_hmac($algo, $data, $key)) === FALSE){
            return null;
        }
        return $signature;
    }

    protected function generateIV(int $len): string
    {
        $iv = '';
        for ($i = 0; $i < $len; ++$i) {
            $iv .= rand(0, 9);
        }
        return (string)$iv;
    }

    public function destroy()
    {
        $this->close();
        setcookie($this->name, '', [
            'expires' => (time() - 86400)
        ]);
        unset($_COOKIE[$this->name]);
    }

}
