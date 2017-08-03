<?php

/**
 * @author zhaozhuobin
 */

namespace JubaopenTech\JWTAuthenticationBundle\Encoder;

use JubaopenTech\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use JubaopenTech\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use JubaopenTech\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use JubaopenTech\JWTAuthenticationBundle\Signature\LoadedJWS;

/**
 * Default Json Web Token encoder/decoder.
 *
 * @author zhaozhuobin
 */
class DefaultEncoder implements JWTEncoderInterface
{
    /**
     * @var string
     */
    private $state = LoadedJWS::INVALID;

    /**
     * @var JWSProviderInterface
     */
    protected $jwsProvider;

    /**
     * @param JWSProviderInterface $jwsProvider
     */
    public function __construct(JWSProviderInterface $jwsProvider)
    {
        $this->jwsProvider = $jwsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function encode(array $payload)
    {
        try {
            $jws = $this->jwsProvider->create($payload);
        } catch (\InvalidArgumentException $e) {
            throw new JWTEncodeFailureException(JWTEncodeFailureException::INVALID_CONFIG, 'An error occured while trying to encode the JWT token. Please verify your configuration (private key/passphrase)', $e);
        }

        if (!$jws->isSigned()) {
            throw new JWTEncodeFailureException(JWTEncodeFailureException::UNSIGNED_TOKEN, 'Unable to create a signed JWT from the given configuration.');
        }

        return $jws->getToken();
    }

    /**
     * {@inheritdoc}
     */
    public function decode($token)
    {
        try {
            $jws = $this->jwsProvider->load($token);
        } catch (\Exception $e) {
            throw new JWTDecodeFailureException(JWTDecodeFailureException::INVALID_TOKEN, 'Invalid JWT Token', $e);
        }

        if ($jws->isInvalid()) {
            throw new JWTDecodeFailureException(JWTDecodeFailureException::INVALID_TOKEN, 'Invalid JWT Token');
        }

        if ($jws->isExpired()) {
            throw new JWTDecodeFailureException(JWTDecodeFailureException::EXPIRED_TOKEN, 'Expired JWT Token');
        }

        if (!$jws->isVerified() && !$jws->isRefresh()) {
            throw new JWTDecodeFailureException(JWTDecodeFailureException::UNVERIFIED_TOKEN, 'Unable to verify the given JWT through the given configuration. If the "jbp_jwt_authentication.encoder" encryption options have been changed since your last authentication, please renew the token. If the problem persists, verify that the configured keys/passphrase are valid.');
        }

        $this->state = $jws->getState();

        return $jws->getPayload();
    }

    public function getState()
    {
        return $this->state;
    }
}
