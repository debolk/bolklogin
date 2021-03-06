<?php
/**
 * @uri /ictcom
 */
class Ictcom extends DefaultResource {
    public $groups = array(
        'cn=nieuwedelft-bestuur,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=ictcom,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=beheer,ou=groups,dc=bolkhuis,dc=nl',
        'cn=oauth-test,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
    );

    /**
     * @method GET
     * @method POST
     * @method OPTIONS
     */
    function checkIctcom() {
        $token = $this->app->server->getAccessTokenData(OAuth2\Request::createFromGlobals());
        return $this->checkAuthorized();
    }
}
