<?php
/**
 * @uri /bestuur
 */
class Bestuur extends DefaultResource {
    public $dns = array(
        'cn=nieuwedelft-bestuur,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=beheer,ou=groups,dc=bolkhuis,dc=nl',
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
