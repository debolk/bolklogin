<?php
/**
 * @uri /ictcom
 */
class Ictcom extends DefaultResource {
    public $groups = array(
        'cn=bestuur,ou=groups,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
        'cn=ictcom,ou=groups,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
        'cn=beheer,ou=groups,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
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
