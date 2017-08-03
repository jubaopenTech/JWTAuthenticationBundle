<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Signature;

/**
 * Object representation of a JSON Web Signature loaded from an
 * existing JSON Web Token.
 */
final class LoadedJWS
{
    const VERIFIED      = 'verified';
    const EXPIRED       = 'expired';
    const REFRESH       = 'refresh';
    const INVALID       = 'invalid';

    /**
     * @var array
     */
    private $payload;

    /**
     * @var int
     */
    private $refreshTtl;

    /**
     * @var string
     */
    private $state;

    /**
     * @var bool
     */
    private $hasLifetime;

    /**
     * @param array $payload
     * @param int  $refreshTtl
     * @param bool  $isVerified
     * @param bool  $hasLifetime
     */
    public function __construct(array $payload, $refreshTtl,$isVerified, $hasLifetime = true)
    {
        $this->payload     = $payload;
        $this->refreshTtl  = $refreshTtl;
        $this->hasLifetime = $hasLifetime;

        if (true === $isVerified) {
            $this->state = self::VERIFIED;
        }

        $this->checkIssuedAt();
        $this->checkExpiration();
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return self::VERIFIED === $this->state;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        $this->checkExpiration();

        return self::EXPIRED === $this->state;
    }

    /**
     * @return bool
     */
    public function isRefresh()
    {
        return self::REFRESH === $this->state;
    }

    /**
     * @return bool
     */
    public function isInvalid()
    {
        return self::INVALID === $this->state;
    }

    /**
     * Ensures that the signature is not expired.
     */
    private function checkExpiration()
    {
        if (!$this->hasLifetime) {
            return;
        }

        if (!isset($this->payload['exp']) || !is_numeric($this->payload['exp'])) {
            return $this->state = self::INVALID;
        }

        if (0 <= (new \DateTime())->format('U') - $this->payload['exp']) {
            return $this->state = self::EXPIRED;
        }

        if (0 <= (new \DateTime())->format('U') - ($this->payload['exp'] - $this->refreshTtl)) {
            return $this->state = self::REFRESH;
        }
    }

    /**
     * Ensures that the iat claim is not in the future.
     */
    private function checkIssuedAt()
    {
        if (isset($this->payload['iat']) && (int) $this->payload['iat'] > time()) {
            return $this->state = self::INVALID;
        }
    }

    public function getState()
    {
        return $this->state;
    }
}
