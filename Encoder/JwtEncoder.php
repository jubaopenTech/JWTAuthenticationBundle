<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Encoder;

use Symfony\Component\Security\Core\Encoder\BasePasswordEncoder;

/**
 * PlaintextPasswordEncoder does not do any encoding.
 */
class JwtEncoder extends BasePasswordEncoder
{

    /**
     * 加密
     * @param string $raw
     * @param string $salt
     * @return string
     */
    public function encodePassword($raw, $salt)
    {
        return md5($raw.$salt);
    }

    /**
     * 验证
     * @param string $encoded
     * @param string $raw
     * @param string $salt
     * @return bool
     */
    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded == $this->encodePassword($raw,$salt);
    }
}
