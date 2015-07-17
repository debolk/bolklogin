<?php
/**
 * @uri /bestuur
 */
class Bestuur extends DefaultResource {
    public $groups = array(
        'cn=nieuwedelft-bestuur,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=beheer,ou=groups,dc=bolkhuis,dc=nl',
        'cn=oauth-test,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
    );

    /**
     * @method GET
     * @method POST
     * @method OPTIONS
     */
    function checkBekend() {
        return $this->checkAuthorized();
    }
}
