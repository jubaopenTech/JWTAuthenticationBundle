<?php

/**
 * @author zhaozhuobin
 */

namespace JWTAuthenticationBundle\Services\JWSProvider;

use JWTAuthenticationBundle\Services\KeyLoader\KeyLoaderInterface;
use JWTAuthenticationBundle\Signature\CreatedJWS;
use JWTAuthenticationBundle\Signature\LoadedJWS;
use Namshi\JOSE\JWS;

/**
 * JWS Provider, Namshi\JOSE library integration.
 * Supports OpenSSL and phpseclib crypto engines.
 *
 * @final
 */
class DefaultJWSProvider implements JWSProviderInterface
{
    /**
     * @var KeyLoaderInterface
     */
    private $keyLoader;

    /**
     * @var string
     */
    private $cryptoEngine;

    /**
     * @var string
     */
    private $signatureAlgorithm;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @var int
     */
    private $refresh_ttl;

    /**
     * @param KeyLoaderInterface $keyLoader
     * @param string             $cryptoEngine
     * @param string             $signatureAlgorithm
     * @param int                $ttl
     * @param int                $refresh_ttl
     *
     * @throws \InvalidArgumentException If the given algorithm is not supported
     */
    public function __construct(KeyLoaderInterface $keyLoader, $cryptoEngine, $signatureAlgorithm, $ttl, $refresh_ttl)
    {
        if (null !== $ttl && !is_numeric($ttl)) {
            throw new \InvalidArgumentException(sprintf('The TTL should be a numeric value, got %s instead.', $ttl));
        }

        $cryptoEngine = $cryptoEngine == 'openssl' ? 'OpenSSL' : 'SecLib';

        if (!$this->isAlgorithmSupportedForEngine($cryptoEngine, $signatureAlgorithm)) {
            throw new \InvalidArgumentException(
                sprintf('The algorithm "%s" is not supported for %s', $signatureAlgorithm, $cryptoEngine)
            );
        }

        $this->keyLoader          = $keyLoader;
        $this->cryptoEngine       = $cryptoEngine;
        $this->signatureAlgorithm = $signatureAlgorithm;
        $this->ttl                = $ttl;
        $this->refresh_ttl        = $refresh_ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $payload)
    {
        $jws    = new JWS(['alg' => $this->signatureAlgorithm], $this->cryptoEngine);
        $claims = ['iat' => time()];

        if (null !== $this->ttl) {
            $claims['exp'] = time() + $this->ttl;
        }

        $jws->setPayload($payload + $claims);
        $jws->sign(
            $this->keyLoader->loadKey('private'),
            $this->keyLoader->getPassphrase()
        );

        return new CreatedJWS($jws->getTokenString(), $jws->isSigned());
    }

    /**
     * {@inheritdoc}
     */
    public function load($token)
    {
        $jws = JWS::load($token, false, null, $this->cryptoEngine);

        return new LoadedJWS(
            $jws->getPayload(),
            $this->refresh_ttl,
            $jws->verify($this->keyLoader->loadKey('public'), $this->signatureAlgorithm),
            null !== $this->ttl
        );
    }

    /**
     * @param string $cryptoEngine
     * @param string $signatureAlgorithm
     *
     * @return bool
     */
    private function isAlgorithmSupportedForEngine($cryptoEngine, $signatureAlgorithm)
    {
        $signerClass = sprintf('Namshi\\JOSE\\Signer\\%s\\%s', $cryptoEngine, $signatureAlgorithm);

        return class_exists($signerClass);
    }
}
