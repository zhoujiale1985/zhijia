<?php
/**
 * JWT实用工具类
 */

namespace App\Tools;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Signer\Hmac\Sha256;

use RuntimeException;
use InvalidArgumentException;

class Jwt
{
    // token 相关的信息
    public $issuer       = 'zhijia';
    public $audience     = 'zhijia';
    public $jwtId        = 'zhijia';
    public $signature    = 'zhijia';
    public $payloadName  = 'payload';
    public $jwtHeaderKey = 'token';
    public $expireTime   = 86400;
    public $signer;

    // token 承载的数据
    public $payloadData = [];

    public function __construct()
    {
        $this->signer = new Sha256();
    }

    /**
     * 创建一个token
     *
     * @param array $payload
     */
    public function createToken($payload = [])
    {
        $token = (new Builder())->setIssuer($this->issuer)
            ->setAudience($this->audience)
            ->setId($this->jwtId, true)
            ->setIssuedAt(time())
            ->setExpiration(time() + $this->expireTime)
            ->set($this->payloadName, $payload)
            ->sign($this->signer, $this->signature)
            ->getToken();
        return (string) $token;
    }

    /**
     * 是否是一个有效的token
     *
     * @param string $token
     *
     * @return boolean
     */
    private function __isValidToken($token) {
        if ($token === null) {
            return false;
        }
        // 解析 token
        try {
            $token = (new Parser())->parse((string) $token);
        } catch (InvalidArgumentException $e) {
            return false;
        } catch (RuntimeException $e) {
            return false;
        }
        // 校验签名
        if (!$token->verify($this->signer, $this->signature)) {
            return false;
        }
        // token 是否过期
        if ($token->isExpired()) {
            return false;
        }
        // 校验基本信息
        $validator = new ValidationData();
        $validator->setIssuer($this->issuer);
        $validator->setAudience($this->audience);
        $validator->setId($this->jwtId);
        $this->payloadData = $token->getClaim('payload');
        $token->validate($validator);
        return (array)$this->payloadData;
    }

    /**
     * 验证 token
     *
     * @return null
     */
    public function checkToken($token)
    {
        return $this->__isValidToken($token);
    }
}