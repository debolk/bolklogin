<?php
class DefaultResource extends Tonic\Resource {

    public $groups = array(
    );

    function checkAuthorized() {
        $req = OAuth2\Request::createFromGlobals();
        if(!$this->app->server->verifyResourceRequest($req))
        {
            return ResourceHelper::OutputToResponse(function() {
                    $this->app->server->getResponse()->send();
            });
        }
        $token = $this->app->server->getAccessTokenData($req);
        $uid = $token['user_id'];
        
        $ldap = LdapHelper::Connect();
        foreach($this->groups as $group)
            if($ldap->memberOf($group, $uid))
                return '';

        return new Tonic\Response(403, '{"error":"unauthorized", "error_message": "The user is not authorized to do this"}');
    }
}
