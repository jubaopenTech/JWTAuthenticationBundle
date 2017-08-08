## JbpJWTAuthenticationBundle

@(使用说明)[JbpJWTAuthenticationBundle]

---------------------------------

#### 1.生成秘钥（公钥和私钥）
定位到console所在目录，执行
```bash
	php console pem:create
```
输入私钥密码生成；

#### 2.引入Bundle
 在AppKernel中的registerBundles方法中加入：
```php
new JubaopenTech\JWTAuthenticationBundle\JbpJWTAuthenticationBundle(),
```
在config.php文件加入以下配置：
```php
jbp_jwt_authentication:
    private_key_path: '%kernel.root_dir%/../var/jwt/private.pem'
    public_key_path:  '%kernel.root_dir%/../var/jwt/private.pem'
    pass_phrase:      '私钥密码'
    token_ttl:        3600
    refresh_ttl:      100
    user_identity_field: username
    encoder:
        service:    jbp_jwt_authentication.encoder.default
        crypto_engine:  openssl
        signature_algorithm: RS256
    token_extractors:
        authorization_header:
            enabled: true
            prefix:  '%authorization_prefix%'
            name:    '%authorization_header_key%'
        cookie:
            enabled: false
            name:    '%authorization_prefix%'
        query_parameter:parameter
            enabled: false
            name:    '%authorization_prefix%'
```

#### 3.获取Token
请求地址：`logincheck`
请求参数：
	`_username:用户名`
	`_password:密码（md5）`
返回值:
```json
	{"token":token}
```

#### 4.请求验证
> 前缀：jwt

请求头部添加`Authorization`参数
```javascript
	$.ajax({
		...
		headers:{
			Authorization:前缀+空格+token
		},
		...
	})
```