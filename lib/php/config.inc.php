<?php

    $whitelist = array(
        '127.0.0.1',
        '::1'
    );

    define ("ENKI", "secret");
    define ("PAYGATE_ID", 10011072130);

    define ("EARLY_BIRD", true);

    define ("EARLY_BIRD_2DAY_PRICE", 5850);
    define ("EARLY_BIRD_3DAY_PRICE", 7650);
    define ("FULL_2DAY_PRICE", 6500);
    define ("FULL_3DAY_PRICE", 8500);

    define ("PHP_ENV", ((in_array($_SERVER['REMOTE_ADDR'], $whitelist)) ? 'DEV' : 'LIVE'));
    $HOST_URLS = array(
        'DEV' => array(
            'REGISTER_HOST_URL' => 'http://localhost:8888',
            'TICKETS_HOST_URL' => 'http://localhost:3000'
        ),
        'LIVE' => array(
            'REGISTER_HOST_URL' => 'https://register.pixelup.co.za',
            'TICKETS_HOST_URL' => 'https://tickets.pixelup.co.za'
        )
    )

?>