<?php

namespace JubaopenTech\JWTAuthenticationBundle\Tests\Functional\DependencyInjection;

use JubaopenTech\JWTAuthenticationBundle\DependencyInjection\JbpJWTAuthenticationExtension;
use JubaopenTech\JWTAuthenticationBundle\JbpJWTAuthenticationBundle;
use JubaopenTech\JWTAuthenticationBundle\Services\JWSProvider\DefaultJWSProvider;
use JubaopenTech\JWTAuthenticationBundle\Tests\Functional\TestCase;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Tests the bundle extension and the configuration of services.
 */
class JbpJWTAuthenticationExtensionTest extends TestCase
{
    private static $resourceDir;

    protected function setUp()
    {
        static::bootKernel();
        self::$resourceDir = sys_get_temp_dir().'/JbpJWTAuthenticationBundle/';

        if (!is_dir(self::$resourceDir)) {
            (new Filesystem())->mkdir(self::$resourceDir);
        }
    }

    public function testEncoderConfiguration()
    {
        /* @var \Symfony\Component\DependencyInjection\ContainerInterface */
        $container          = static::$kernel->getContainer();
        $encoderNamespace   = 'jbp_jwt_authentication.encoder';
        $cryptoEngine       = $container->getParameter($encoderNamespace.'.crypto_engine');
        $signatureAlgorithm = $container->getParameter($encoderNamespace.'.signature_algorithm');

        $jwsProviderMock = $this
            ->getMockBuilder(DefaultJWSProvider::class)
            ->setConstructorArgs([
                $container->get('jbp_jwt_authentication.key_loader'),
                $cryptoEngine,
                $signatureAlgorithm,
                3600,
                100
            ])
            ->getMock();

        $this->assertInstanceOf(
            'JubaopenTech\JWTAuthenticationBundle\Encoder\DefaultEncoder',
            $container->get($encoderNamespace)
        );

        // The configured engine is the one used by the service
        $this->assertAttributeEquals(
            'openssl' == $cryptoEngine ? 'OpenSSL' : 'SecLib',
            'cryptoEngine',
            $jwsProviderMock
        );

        // The configured algorithm is the one used by the service
        $this->assertAttributeEquals(
            $signatureAlgorithm,
            'signatureAlgorithm',
            $jwsProviderMock
        );
    }

    public function testTokenExtractorsConfiguration()
    {
        // Default configuration
        $this->dumpConfig('token_extractors', []);
        $chainTokenExtractor = $this->getContainer('token_extractors')->getDefinition('jbp_jwt_authentication.extractor.chain_extractor');

        $extractorIds = array_map(function ($ref) {
            return (string) $ref;
        }, $chainTokenExtractor->getArgument(0));

        $this->assertContains('jbp_jwt_authentication.extractor.authorization_header_extractor', $extractorIds);
        $this->assertNotContains('jbp_jwt_authentication.extractor.cookie_extractor', $extractorIds);
        $this->assertNotContains('jbp_jwt_authentication.extractor.query_parameter_extractor', $extractorIds);

        // Custom configuration
        $this->dumpConfig('token_extractors', ['token_extractors' => ['authorization_header' => true, 'cookie' => true]]);
        $chainTokenExtractor = $this->getContainer('token_extractors')->getDefinition('jbp_jwt_authentication.extractor.chain_extractor');

        $extractorIds = array_map(function ($ref) {
            return (string) $ref;
        }, $chainTokenExtractor->getArgument(0));

        $this->assertContains('jbp_jwt_authentication.extractor.authorization_header_extractor', $extractorIds);
        $this->assertContains('jbp_jwt_authentication.extractor.cookie_extractor', $extractorIds);
        $this->assertNotContains('jbp_jwt_authentication.extractor.query_parameter_extractor', $extractorIds);
    }

    private function getContainer($file)
    {
        $container = new ContainerBuilder();
        $container->registerExtension(new SecurityExtension());
        $container->registerExtension(new JbpJWTAuthenticationExtension());

        (new JbpJWTAuthenticationBundle())->build($container);
        (new YamlFileLoader($container, new FileLocator([self::$resourceDir])))->load($file.'.yml');

        $container->getCompilerPassConfig()->setOptimizationPasses([]);
        $container->getCompilerPassConfig()->setRemovingPasses([]);
        $container->compile();

        return $container;
    }

    private function dumpConfig($filename, array $configs = [])
    {
        file_put_contents(self::$resourceDir.$filename.'.yml', @Yaml::dump(['jbp_jwt_authentication' => $configs]));
    }

    public static function tearDownAfterClass()
    {
        (new Filesystem())->remove(self::$resourceDir);
    }
}
