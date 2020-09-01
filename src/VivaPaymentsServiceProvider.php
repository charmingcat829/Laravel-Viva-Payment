<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;

class VivaPaymentsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/services.php',
            'services'
        );

        $this->app->singleton(Client::class, function ($app) {
            return new Client(
                $this->buildGuzzleClient(),
                $app->make('config')->get('services.viva.environment')
            );
        });
    }

    /**
     * Build the Guzzlehttp client.
     */
    protected function buildGuzzleClient(): GuzzleClient
    {
        return new GuzzleClient([
            'curl' => $this->curlDoesntUseNss()
                ? [CURLOPT_SSL_CIPHER_LIST => 'TLSv1']
                : [],
        ]);
    }

    /**
     * Check if cURL doens't use NSS.
     *
     * @return bool
     */
    protected function curlDoesntUseNss()
    {
        $curl = curl_version();

        return ! preg_match('/NSS/', $curl['ssl_version']);
    }

    /**
     * Determine if the provider is deferred.
     *
     * @return bool
     */
    public function isDeferred()
    {
        return true;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Client::class];
    }
}
