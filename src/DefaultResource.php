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
                $response = new Tonic\Response(200, json_encode([]));
                $response->AccessControlAllowOrigin = '*';
                return $response;
            }
        $error = [
            'error' => 'unauthorized',
            'error_description' => 'The user is not authorized to do this'
        ];
        $response = new Tonic\Response(403, json_encode($error));
        $response->AccessControlAllowOrigin = '*';
        return $response;
    }
}
