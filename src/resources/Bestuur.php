<?php
/**
 * @uri /bestuur
 */
class Bestuur extends DefaultResource {
    public $groups = array(
        'cn=bestuur,ou=groups,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
        'cn=beheer,ou=groups,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
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
