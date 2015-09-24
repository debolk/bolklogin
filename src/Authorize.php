<?php
/**
 * @uri /authorize
 *
 * CAUTION:
 * This endpoint is obsolete. There are no known clients which use this process.
 * It is provided here to support clients unknown to us. New clients MUST follow
 * the new process as described in the documentation (https://auth.debolk.nl),
 * which is handled by the Authenticate class (src/Authenticate.php)
 */
class Authorize extends Tonic\Resource {

    /**
     * @method GET
     */
    function getAuthorize() {
        if(!isset($_SERVER['PHP_AUTH_USER'])) {
            $result = new Tonic\Response(401, "Om deze service te bekijken is inloggen verplicht, refresh om opnieuw in te loggen.");
            $result->wwwAuthenticate = 'Basic realm="De Bolk"';
            return $result;
        }

        $ldap = LdapHelper::Connect();
        if(!$ldap->bind($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']))
        {
            $result = new Tonic\Response(401, "Authenticatie mislukt");
            $result->wwwAuthenticate = 'Basic realm="De Bolk - Authenticatie mislukt"';
            return $result;
        }

        return ResourceHelper::OutputToResponse(function() {

            $req = OAuth2\Request::createFromGlobals();
            $response = new OAuth2\Response();

            if($this->app->server->validateAuthorizeRequest($req, $response))
            {

                $this->app->server->handleAuthorizeRequest($req, $response, true, $_SERVER['PHP_AUTH_USER']);
            }

            $response->send();
        });
    }
}
