<?php
/**
 * @uri /bestuur
 */
class Bestuur extends DefaultResource {
    public $groups = array(
        'cn=nieuwedelft-bestuur,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=beheer,ou=groups,dc=bolkhuis,dc=nl',
    );

    /**
     * @method GET
     * @method POST
     */
    function checkBekend() {
        // Workaround for dkarrenbeld testing
        $token = $this->app->server->getAccessTokenData(OAuth2\Request::createFromGlobals());
        if($token['user_id'] == 'jakob')
        {
          $response = new Tonic\Response(200, '{}');
          $response->AccessControlAllowOrigin = '*';
          return $response;
        }

        return $this->checkAuthorized();
    }
}
