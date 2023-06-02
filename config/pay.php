<?php
declare(strict_types=1);

use Yansongda\Pay\Pay;

return [
    'alipay' => [
        'default' => [
            // 必填-支付宝分配的 app_id
            'app_id' => '9021000122681666',
            // 必填-应用私钥 字符串或路径
            'app_secret_cert' => 'MIIEogIBAAKCAQEApcFxnpabd7WA7TywxmGkATH4Ux8qyX9FlhGSo1j8VCGd9d4ItHtJ/Yk1l1xPF71r8fNC1rrO3s2uP+r30dIaryMdf+ISY2EoRFwE6Z38LuQzFHXodf078k3J2R5eSX7vaYftmopmPEY8bciDaU4XUlQ/gA3J68pzIm79Ntrq1+u6Ty5p+RHdLFKDcMaPbIWwIZOMQjR+yYyAooYexycr/TPNm7HkfJ/4lesSedDwLYUNP78m7icDRywoF8q3ZeJMNS2xwFkzy+vTwYsJ9EgngCtPzHSUkm+3/ExvzVAjbHcda1LYuVCmD1Srx9kNuXgqFaD/M7n4EM73WHltElTttwIDAQABAoIBAAJpsHe4hYbWk4vgiEo6/aIu0giTuZG5Uo/+rX3HO9UVsDD2DHZkHQ2dw7PyWgxka3/YJK7vhTdlExZHG1hokI7gGfvUlKPxtB5aoI+uBw+/rqarmLiu+QJr83Y/pOu490839VN8lERoWVTXyGHuCxTduRbqHHhcOBTWPMVhpm2CMNjkZm1otsWljdnm7gO+zcq+00gP5SckyHjDahX4TepfwKpQsJlqh9fS9yQAGok48HN8xnoHMH1OdyDDN8p1+Vv1U/DvIV+VZ+B0NiVb0m87rxarcjyPUmFa7F4EiRC6Xl3q8BEWVFv6XJ0gYDH/m+zzbjJduQRPV42M9BmNbBECgYEA93oM71r3L00KHDgNYVOw8X4hakc71XhOYDTLyjrU/n7hJsELNGH7UItqQSnrhezeH4Teg5hWuehHBzCC+KSAXYCft8Ze5ILTZlucy+SQ4imcByU5Trh6KX0jO79Ecta89kVg0N/O/rwoOm2D2vEn4vuI7Txwq32Vof53ltRCi8sCgYEAq3bgL4hUl8VvLfEiZKhxlOXUVt/SOwSDdmuclacM+coVuigbcL8aY8lY2AelZ7/84uG6AZOfTyQjzeyN5xfaMuH9GQi3rY0LsQ72qx/TO+5pF7hDFHl5VFpQ/iIyk80oarGruo9WVJetFso8J/LDr5sSZTUG6lo9IIRFBEMHwEUCgYBeGPtIG4d3ZcydVDbKNf9Go5XjCjjW/0qVzB1cxKfuKKJFkQtInKTpSElbg6v5HUqMI4JT+R2ozIgNVMXH8wyYAOs5/mRgcNoexmDFKiBozjd0hBPZOc3BbsJx2lUVcU8iONkKMr9LHpIRPUjwe9eVt9ylj+CrZDH8CXzBTe4LpwKBgC9Y3yUg/0L9qOrFyFqFTP/xywXGPnY/k9Gye4WzoFilngROqO0kSDQ/2EGnMtyIXluEO2nOCtK+xwhJBxJGOuGMF+i+yIGrDgxxdlngquLEvc5n+lYACSnq2qyiYtb/cuarcyFMDWnEjG5bn+rkFXc7WgQKdYNnMbP18lzv6YcFAoGAPJHCCkjomyvltNNsjmTERjMMAv54kqHc9qWjTYT3Md20Ck8IBI7GEVRw6vXuvK+qQM8lPdWWTh2rz9nQY0CYaokGBcK+4NJFkdcN+80OEImbJf+zXhtURcVkgylXBZ1/Iyu/N/Ns5L211r4qDkK93/VdM4qzn2Me9V9FnaD+o78=',
            // 必填-应用公钥证书 路径
            'app_public_cert_path' => '/appPublicCert.crt',
            // 必填-支付宝公钥证书 路径
            'alipay_public_cert_path' => '/alipayPublicCert.crt',
            // 必填-支付宝根证书 路径
            'alipay_root_cert_path' => '/alipayRootCert.crt',
            'return_url' => '',
            'notify_url' => '',
            // 选填-第三方应用授权token
            'app_auth_token' => '',
            // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
            'service_provider_id' => '',
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => Pay::MODE_SANDBOX,
        ]
    ],
    'wechat' => [
        'app_id' => '',
        'mch_id' => '',
        'key' => '',
        'cert_client' => '',
        'cert_key' => '',
        'log' => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];