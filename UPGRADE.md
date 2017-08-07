UPGRADE FROM 1.x to 2.0
=======================

Configuration
-------------

* The JWT authentication system has been deprecated in favor of a Guard authenticator  
  called `JWTTokenAuthenticator`.  
  By the way, the security configuration has been simplified. Most of the options that was  
  set from the JWT-secured firewall configuration have been moved to the bundle configuration,  
  keeping the same names and default values.
  
  __Removed options__
  - `create_entry_point`: The new authenticator being an entry point after all, this option doesn't bring any value anymore.  
  If a firewall allows anonymous, the entry point will not be called at all, letting the request continue.  
  If it doesn't, the entry point will dispatch a `on_jwt_not_found` event that can be subscribed to customize the default failure response that will be returned by the entry point.
  - `throw_exceptions`: This option doesn't make sense anymore as the exceptions thrown during the authentication process are needed, involving call of the good method in the good time, dispatching the good events, so a custom response can be easily set, as its content no more depends on the exception thrown.
  - `authentication_provider` and `authentication_listener`: It's now part of the authenticator role, simplifiying a lot the corresponding code that can now be found/overriden from one place.

  __Before__

  ```yaml
  # app/config/security.yml
  firewalls:
      api:
          jbp_jwt:
              authorization_header: ~
              cookie: ~
              query_parameter: ~
              throw_exceptions: false
              create_entry_point: true
              authentication_provider: jbp_jwt_authentication.security.authentication.provider
              authentication_listener: jbp_jwt_authentication.security.authentication.listener
  ```

  __After__

  ```yaml
  # app/config/security.yml
  firewalls:
      api:
          guard:
              authenticators:
                  - jbp_jwt_authentication.jwt_token_authenticator

  # app/config/config.yml
  jbp_jwt_authentication:
      # ...
      token_extractors:
          authorization_header: ~
          cookie: ~
          query_parameter: ~
  ```
  
* The `token_ttl` option __must__ be a numeric value, having an infinite token lifetime is no more
  
Events
-------

* The ability of retrieving `Request` instances from `Event` classes has been removed,
  as the current `Request` is no more injected into when they are dispatched.  
  Being able to access them was mainly useful for doing stuff depending on informations 
  retrieved from.  
  Fortunately, you can reproduce the same behaviour in a more efficient way:

  __Before__
  
  ```yaml
  services:
      jwt_event_listener:
          class: AppBundle\EventListener\JWTCreatedListener
          tags:
              - { name: kernel.event_listener, event: jbp_jwt_authentication.on_jwt_created, method: onJWTCreated }
  ```
  
  ```php
  use JubaopenTech\JWTAuthenticationBundle\Event\JWTCreatedEvent;
  
  class JWTCreatedListener
  {
      public function onJWTCreated(JWTCreatedEvent $event)
      {
          $request = $event->getRequest();
      }
  }
  ```
  
  __After__
  
  ```yaml
  services:
      jwt_event_listener:
          class: AppBundle\EventListener\JWTCreatedListener
          arguments: [ '@request_stack' ]
          tags:
              - { name: kernel.event_listener, event: jbp_jwt_authentication.on_jwt_created, method: onJWTCreated }
  ```
  
  ```php  
  use JubaopenTech\JWTAuthenticationBundle\Event\JWTCreatedEvent;
  use Symfony\Component\HttpFoundation\RequestStack;

  class JWTCreatedListener
  {
      private $requestStack;
      
      public function __construct(RequestStack $requestStack)
      {
          $this->requestStack = $requestStack;
      }
      
      public function onJWTCreated(JWTCreatedEvent $event)
      {
          $request = $this->requestStack->getCurrentRequest();
      }
  }
  ```

* Introduced JWTExpiredEvent
  In 1.x, trying to authenticate an user with an expired token was causing a JWTInvalidEvent to be dispatched, 
  as for several other mixed reasons. Now in 2.x, this failure reason has its own event on which you can listen on.
  
Encoder
-------

* The service `jbp_jwt_authentication.jwt_encoder` has been removed in favor  
  of `jbp_jwt_authentication.encoder.default` that supports OpenSSL and  
  phpseclib crypto engines.
  
* The class `JWTAuthenticationBundle\Encoder\JWTEncoder` has been
  removed in favor of `JWTAuthenticationBundle\Encoder\DefaultEncoder`.

  It was used by the `jbp_jwt_authentication.jwt_encoder` service that has been removed.  
  
* The `JWTAuthenticationBundle\Encoder\JWTEncoderInterface` has been changed,
  the `encode` and `decode` methods now throw exceptions rather than returning `false`  
  in case of error.
  
* The `JWTAuthenticationBundle\Encoder\DefaultEncoder` default encoder used via service
  "jbp_jwt_authentication.encoder.default" now checks for a `iat` claim existance and validity when decoding a token
  using `DefaultEncoder::decode()`.

KeyLoader
---------

* The `jbp_jwt_authentication.openssl_key_loader` has been removed  
  in favor of `jbp_jwt_authentication.key_loader`.

* The class `JubaopenTech\JWTAuthenticationBundle\Services\OpenSSLKeyLoader` has been
  removed in favor of `JubaopenTech\JWTAuthenticationBundle\Services\KeyLoader\OpenSSLKeyLoader`.
  
  It was used by the `jbp_jwt_authentication.openssl_key_loader` that has been removed.
  
Command
-------

* The `jbp:jwt:check-open-ssl` command has been renamed to `jbp:jwt:check-config`  
  as the bundle now supports several crypto engines.

Security
--------

* The `JWTManagerInterface` has been deprecated in favor of a new `JWTTokenManagerInterface` 
  implementing two new methods: `setUserIdentityField` and `getUserIdentityField`.
  These methods were already implemented by the JWTManager class in 1.x but not guaranteed
  by the old interface.

* The `JWTManager` is no more responsible of setting the token 
  `exp` claim, meaning that its constructor takes one less argument (the last one). This logic has been moved to the `Encoder` that is responsible of creating signed tokens and verifying/validating existing ones.
