<?php
/**
 * @uri /bekend
 */
class Bekend extends DefaultResource {
    public $groups = array(
        'cn=leden,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=oud-leden,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=kandidaatleden,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
    );

    /**
     * @method GET
     * @method POST
     */
    function checkBekend() {
        return $this->checkAuthorized();
    }
}