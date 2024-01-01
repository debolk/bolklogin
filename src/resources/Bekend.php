<?php
/**
 * @uri /bekend
 */
class Bekend extends DefaultResource {
    public $dns = array(
        'ou=people,ou=leden,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'ou=people,ou=oudleden,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'ou=people,ou=kandidaatleden,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'ou=people,ou=donateurs,o=nieuwedelft,dc=bolkhuis,dc=nl',
        'ou=people,ou=ledenvanverdienste,o=nieuwedelft,dc=bolkhuis,dc=nl',
    );

    /**
     * @method GET
     * @method POST
     * @method OPTIONS
     */
    function checkBekend() {
        $this->checkAuthorized();
    }

    function checkAuthLDAP($ldap, $uid, $dn)
    {
        return $ldap->userExists($dn, $uid);
    }
}
