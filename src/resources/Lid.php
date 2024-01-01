<?php
/**
 * @uri /lid
 */
class Lid extends DefaultResource {
    public $dns = array(
        'ou=people,ou=leden,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'ou=people,ou=kandidaatleden,o=nieuwedelft,dc=bolkhuis,dc=nl',
    );

    /**
     * @method GET
     * @method POST
     * @method OPTIONS
     */
    function checkLid() {
        return $this->checkAuthorized();
    }
}
