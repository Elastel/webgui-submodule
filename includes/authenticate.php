<?php

if (RASPI_AUTH_ENABLED) {
    $auth = new \ElastPro\Auth\HTTPAuth;

    if (!$auth->isLogged()) {
        $auth->authenticate();
    }
}