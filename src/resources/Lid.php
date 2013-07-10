<?php
/**
 * @uri /lid
 */
class Lid extends DefaultResource {
    public $groups = array(
        'cn=leden,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'cn=kandidaatleden,ou=groups,o=nieuwedelft,dc=bolkhuis,dc=nl',
    );

    /**
     * @method GET
     * @method POST
     */
    function checkLid() {
        return $this->checkAuthorized();
    }
}
