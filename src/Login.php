<?php
/**
 * @uri /authenticate
 */
class Login extends Tonic\Resource
{

    /**
     * Shows the login page
     * @method GET
     * @method POST
     */
    public function getLoginPage()
    {
        session_start();

        // Process request into something useable
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();

        // Check parameters for completeness
        if (! $this->validateParameters($_GET)) {
            return $this->displayError('Required parameters missing or not a known client');
        }

        // Return a token if the user has authorized this application before
        if ($this->app->server->validateAuthorizeRequest($request, $response)) {
            return $this->returnToken($response);
        }

        if (! $this->loggedIn()) {
            // Attempt to login
            if (isset($_POST['username'])) {
                $ldap = LdapHelper::Connect();
                if(@$ldap->bind($_POST['username'], $_POST['password'])) {
                    $_SESSION['user_id'] = $_POST['username'];
                }
                else {
                    return $this->loginForm('Gebruikersnaam en/of wachtwoord niet correct');
                }
            }
            return $this->loginForm();
        }

        // If this is a authorization form submission
        if (isset($_POST['authorization'])) {
            $authorization = ($_POST['authorization'] == '1');
            $this->app->server->handleAuthorizeRequest($request, $response, $authorization, $_SESSION['user_id']);
            return $this->returnToken($response);
        }

        // Show the authorization form
        return $this->authorizationForm();
    }

    /**
     * Returns the login form
     * @return string complete HTML form
     */
    private function loginForm($error = null)
    {
        ob_start();
        include('../public/login.php');
        return ob_get_clean(); 
    }

    /**
     * Returns the authorization form
     * @return string complete HTML form
     */
    private function authorizationForm()
    {
        ob_start();
        include('../public/authorize.php');
        return ob_get_clean();
    }

    private function loggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Displays a user-friendly error page
     * @param  string $error_message error message to display
     * @return string                complete HTML page
     */
    private function displayError($error_message)
    {
        ob_start();
        include('../public/error.php');
        return ob_get_clean();
    }

    /**
     * Returns a valid access token
     * @return Response the response containing the token
     */
    private function returnToken($response)
    {
        return ResourceHelper::OutputToResponse(function() use ($response) {
            $response->send();
        });
    }

    /**
     * Validates whether all four expected parameters are present in an array
     * The required keys are client_id, client_secret, redirect_url and state
     * Extra given parameters are ignored
     * @param  array $parameters
     * @return boolean
     */
    private function validateParameters($parameters)
    {
        $required = ['client_id' => null, 'client_secret' => null, 'redirect_url' => null, 'state' => null];

        // Strip all non-required keys
        $parameters = array_intersect_key($parameters, $required);

        // Strip all FALSE values
        $parameters = array_filter($parameters);
        
        return (sizeof($parameters) === 4);
    }
}
