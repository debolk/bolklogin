<?php

use OAuth2\Storage\ScopeInterface;

/**
 * Class that manages all available scopes
 */
class Scope implements OAuth2\Storage\ScopeInterface, OAuth2\ScopeInterface
{
    /**
     * Internal storage for all valid scopes
     * @var array
     */
    private $scopes;

    public function __construct()
    {
        $this->scopes = [
            [
                'slug'        => 'bolknoms.registrations.read',
                'name'        => 'Bolknoms',
                'description' => 'Lezen van al jouw aanmeldingen',
                'includes'    => null
            ],
            [
                'slug'        => 'bolknoms.registrations.write',
                'name'        => 'Bolknoms',
                'description' => 'Lezen en bewerken van al jouw aanmeldingen. Nieuwe aanmeldingen maken',
                'includes'    => ['bolknoms.registrations.read']
            ],
        ];
    }

    /**
     * There's no default scope
     * @param  string $client_id
     * @return null
     */
    public function getDefaultScope($client_id = null)
    {
        return null;
    }

    /**
     * Determine whether a scope exists
     * @param  string $scope
     * @param  string $client_id ignored
     * @return boolean
     */
    public function scopeExists($scope, $client_id = null)
    {
        // Gather all slugs
        $scopes = array_map(function($scope){
            return $scope['slug'];
        }, $this->scopes);

        // Determine presence
        return in_array($scope, $scopes);
    }

    public function checkScope($required_scope, $available_scope)
    {

    }

    public function getScopeFromRequest(RequestInterface $request)
    {

    }
}
