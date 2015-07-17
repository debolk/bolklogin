<?php
/**
 * @uri /bekend
 */
class Bekend extends DefaultResource {
    public $groups = array(
        'cn=leden,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=oud-leden,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=kandidaatleden,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
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
