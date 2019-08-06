<?php

return [
    'alipay' => [
        'app_id'         => '2016100100641115',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsHmExcLEqmcKHcGuutW3jifDlYEhEXiT4HzvpSqEsWwiBUcaBxoVpvZpwXqu5CwQqYLPBBBQvIsx0EuzQWo7yyCazDKCDmgNSCL0VJEolDh+oLWo/BO0bL3UvsW72CovXDtyAaqKrRaeUfFqHvzphA/MQD6FiXFeAVae+fITQHDHsnV2qn3SsX9r5kcJQu6NRd0UWKLC5prZhZfwADaFaP+cNAU5eJWwtPmWb84szuVR6Cpq5exUpeQc7yF/DUm84MYkFypHjOeqxHSMxz0CcjZeVDXW/4dZ1WLC/rtUEMwF63Ter0qozZC6WVhl0koLL6b8zwtrEFLVbB8bWuhjgwIDAQAB',


        'private_key'    => 'MIIEowIBAAKCAQEAxtVAHZOTrKaNuQ2Phov3z/hpFmtLKGOmJALPfs6HbAW0I7f/0vz8I0dDmvaCil3ImavSEzy947RL2g9v5J7HqYO8ZFaLq8IwG0uvkdYq3LYGPRKFcKXMEQXNkpvI2Z9v3v2MtwCn0G958X6Uw8SIOwbkWb5JpoGPdj/YMrERUWiOkZRus783462nrI+mdrD4UTb0U4/SsCkzEeofgTuYxWiefhlk8JPDbhAhZKGIUXJ78upU4lndrxquPGlO+VqAn0VxeyD8nLyu9UIY3iz5I7XLYpQ1aHhQTpL4jUTZta2D3/OOHZViuVqV8Zdr/7lXA5k0eHCgHUgzO2m1ap836wIDAQABAoIBAC1EDzyshEoiANHITIyNAPXPz9zSwGGJjiFUonhz/FCTW9dl5E/cWGqPpsx4om9tumWBwGmwwmo8eahDK24Y18u3ugP9z/5iTyFnsai54TguGG1+8fIbTMHzWEGwRaGFsqpJfueKtqm1N0XLU4LeE0Wc5A+qSoolpMt+7XFc7lxh0ltSokoErjKnYFELUTucsGsg2eCEh5SJhDQ34C8ahsSftO4xoaz34eRvXFaPZLJWrR95EMC3eIQEA6zNuRDm+1bDNa7CHG+8AG23F3LtyuLKFxMxygng5j4nv7lk38+5slUu/H9gUdqoodNyCVgdcdT64dY3X9ZG75rbOUWvLYECgYEA8aKDphGohltvw6bdFF60gNb19sRwLI577O/x5+wea/sMrTI82uMRfjLu3aIanT/vH9WrFpFbMMma4vcUoPrD1JvXbQYJwg2RDjbFuQNPbGfs44nQhIb+PFunN2pjSNecj2sx4zQWjjxSvez2W5ELtqYyXSehnPqDPUbfFVJbvpkCgYEA0qdTz2qmrmLyeyTuhD+N5h3aOuSq1ZtpluI1XKlh+0CSIr92sMkfu8EUWTV9zYbRMtRAv91Gt/SFSjowNCBa44HgSIHcJll9+/8CQcJXnb6U/MH2qfRefEluvfWkUygGww9LdAi1k+wsR4t+qmg/Xm+Tv0Z+MkIWQhtWzjNMESMCgYBZ3Pui5kxLc3P0SnlL2xC8HbWpyVKCb5cM/gt0Gi8SL3J+cwGI0guuaFjHWkMVmjJfH4KaSQKl+UQ+Bm/IkpoD1tVeOXGllpAMvjf4X4/JHDlQQe+naeDhJ6DfjhRQgcc62z3ZZoTA7xRCh/7y/NVjXxm55URIci4eut26Oz/deQKBgQCArHUlXaAzJX24eva3EZs71UBYeRVhJW16HPM7hruzHd4mG44ErtYTef1UALi3soJW6hAjIqbv9wz0KlcHgDwEfHDj2W8AfenW5A4c3PloAeJAArsJVZvhj3l1z3Zw1SJMRCty1V8U3E6v5SWaMdODe+RkEktDp9qwxsJfcjYkwQKBgGAoO2TPU3Is/FkYyfvrtWZjzjbpk7DddCONXonuW3AnaCuCb+yPhIhl8sYhrCS9GOU4XTSWHyT0dFkzhvLqnxkmc8777FwfzAprle9OrEmWt1owg0AScq+jQ63XZepp76o4GjM0pcGpk7e5Uq5PRpeynOU7xLhmqQqI8RBb0x0z',


        'log'            => [
            'file' => storage_path('logs/alipay.log'),
        ],
    ],

    'wechat' => [
        'app_id'      => '',
        'mch_id'      => '',
        'key'         => '',
        'cert_client' => '',
        'cert_key'    => '',
        'log'         => [
            'file' => storage_path('logs/wechat_pay.log'),
        ],
    ],
];
