<?php
/**
 * @uri /mp3control
 */
class Mp3Control extends DefaultResource {
    public $groups = array(
        'cn=ictcom,ou=groups,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
        'cn=bestuur,ou=groups,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
        'cn=beheer,ou=groups,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
    );

    /**
     * @method GET
     * @method POST
     * @method OPTIONS
     */
    function checkMP3Control() {
        $token = $this->app->server->getAccessTokenData(OAuth2\Request::createFromGlobals());
        return $this->checkAuthorized();
    }
}
