<?php

/**
 * Helper functions for Acumen Craft application
 */

if (!function_exists('isOAuthConfigured')) {
    /**
     * Check if OAuth provider is configured
     */
    function isOAuthConfigured(string $provider): bool
    {
        $clientId = config("services.{$provider}.client_id");
        $clientSecret = config("services.{$provider}.client_secret");
        
        return !empty($clientId) && !empty($clientSecret);
    }
}

if (!function_exists('getConfiguredOAuthProviders')) {
    /**
     * Get list of configured OAuth providers
     */
    function getConfiguredOAuthProviders(): array
    {
        $providers = ['google', 'github', 'facebook', 'apple'];
        $configured = [];
        
        foreach ($providers as $provider) {
            if (isOAuthConfigured($provider)) {
                $configured[] = $provider;
            }
        }
        
        return $configured;
    }
}
