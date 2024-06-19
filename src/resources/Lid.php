<?php
/**
 * @uri /lid
 */
class Lid extends DefaultResource {
    public $groups = array(
        'ou=people,ou=leden,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
        'ou=people,ou=kandidaatleden,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
        'ou=people,ou=ledenvanverdienste,o=nieuwedelft,dc=i,dc=bolkhuis,dc=nl',
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
