<?php
/**
 * @uri /mp3control
 */
class Mp3Control extends DefaultResource {
    public $dns = array(
        'cn=ictcom,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=nieuwedelft-bestuur,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=beheer,ou=groups,dc=bolkhuis,dc=nl',
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
