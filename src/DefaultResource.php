<?php
class DefaultResource extends Tonic\Resource {

    public $dns = array(
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
        foreach($this->dns as $dn)
            if($this->checkAuthLDAP($ldap, $uid, $dn))
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

    function checkAuthLDAP($ldap, $uid, $dn){
        return $ldap->memberOf($dn, $uid);
    }
}
