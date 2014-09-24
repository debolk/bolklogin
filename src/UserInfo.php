<?php

use Tonic\Resource;
use Tonic\Response;

/**
 * @uri /userinfo
 */
class UserInfo extends Resource
{
    /**
     * @method GET
     * @return Response containing the user ID
     */
    public function getInfo()
    {
        if (!$this->app->server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            return ResourceHelper::OutputToResponse(function(){
                return $this->app->server->getResponse()->send();
            });
        }

        $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
        return new Response(200, $token['user_id']);
    }
}
