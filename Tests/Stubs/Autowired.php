<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Stubs;

use JubaopenTech\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use JubaopenTech\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use JubaopenTech\JWTAuthenticationBundle\Services\JWTManager;
use JubaopenTech\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use JubaopenTech\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;

class Autowired
{
    private $jwtManager;
    private $jwtEncoder;
    private $tokenExtractor;
    private $jwsProvider;

    public function __construct(JWTTokenManagerInterface $jwtManager, JWTEncoderInterface $jwtEncoder, TokenExtractorInterface $tokenExtractor, JWSProviderInterface $jwsProvider)
    {
        $this->jwtManager = $jwtManager;
        $this->jwtEncoder = $jwtEncoder;
        $this->tokenExtractor = $tokenExtractor;
        $this->jwsProvider = $jwsProvider;
    }

    public function getJWTManager()
    {
        return $this->jwtManager;
    }

    public function getJWTEncoder()
    {
        return $this->jwtEncoder;
    }

    public function getTokenExtractor()
    {
        return $this->tokenExtractor;
    }

    public function getJWSProvider()
    {
        return $this->jwsProvider;
    }
}
