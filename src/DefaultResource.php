<?php
class DefaultResource extends Tonic\Resource {

    public $groups = array(
    );

    function checkAuthorized() {
        $req = OAuth2\Request::createFromGlobals();
        if(!$this->app->server->verifyResourceRequest($req))
        {
            $response = ResourceHelper::OutputToResponse(function() {
                    $this->app->server->getResponse()->send();
            });
            $response->AccessControlAllowOrigin = '*';
            return $response;
        }
        $token = $this->app->server->getAccessTokenData($req);
        $uid = $token['user_id'];
        
        $ldap = LdapHelper::Connect();
        foreach($this->groups as $group)
            if($ldap->memberOf($group, $uid))
            {
                $response = new Tonic\Response(200, '{}');
                $response->AccessControlAllowOrigin = '*';
                return $response;
            }

        $response = new Tonic\Response(403, '{"error":"unauthorized", "error_description": "The user is not authorized to do this"}');
        $response->AccessControlAllowOrigin = '*';
        return $response;
    }
}
