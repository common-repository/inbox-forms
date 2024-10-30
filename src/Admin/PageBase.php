<?php

namespace Inbox\Admin;

use GuzzleHttp\Client;

if (! defined('ABSPATH')) {
    exit;
}

ob_start();

abstract class PageBase
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $apiClient;

    public function __construct()
    {
        $this->apiClient = new Client([
            'timeout' => 10
        ]);
    }

    /**
     * @return false|string
     */
    public function getApiKey()
    {
        return get_option('inbox_api_key');
    }
}
