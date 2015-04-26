<?php
/**
 * @uri /authenticate
 */
class Authenticate extends Tonic\Resource
{

    /**
     * Main process for authorizing clients: [login page] -> [authorization page] -> [token]
     * @method GET
     * @method POST
     */
    public function getLoginPage()
    {
        session_start();

        // Process request into something useable
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();

        // Check whether all required parameters are supplied
        if (! $this->validateParameters($_GET)) {
            // Display an error page
            return $this->displayError('Required parameters missing');
        }

        // If the login form is submitted
        if (isset($_POST['username'])) {
            // Attempt to login
            if (!$this->login($_POST['username'], $_POST['password'])) {
                return $this->loginForm('Gebruikersnaam en/of wachtwoord niet correct');
            }
        }

        // If we don't have a session, force a login
        if (! $this->loggedIn()) {
            return $this->loginForm();
        }

        // At this point, we have a valid sesssion
        // Check for a prior authorisation, hand out a token if it is
        if ($this->app->server->validateAuthorizeRequest($request, $response)) {
            return $this->returnToken($response);
        }

        // Check for a authorisation form submission, hand out a token if it is
        if (isset($_POST['authorization'])) {
            $authorization = ($_POST['authorization'] == '1');
            $this->app->server->handleAuthorizeRequest($request, $response, $authorization, $_SESSION['user_id']);
            return $this->returnToken($response);
        }

        // If not previously authorised, show the authorisation form
        return $this->authorizationForm();

    }

    /**
     * Login a user
     * @param  string $username the username given
     * @param  string $password the password used
     * @return boolean          whether the attempt succeeded
     */
    private function login($username, $password)
    {
        $ldap = LdapHelper::Connect();
        if(@$ldap->bind($username, $password)) {
            $_SESSION['user_id'] = $_POST['username'];
            return true;
        }
        return false;
    }

    /**
     * Returns the login form
     * @return string complete HTML form
     */
    private function loginForm($error = null)
    {
        ob_start();
        include('../web/views/login.php');
        return ob_get_clean();
    }

    /**
     * Returns the authorization form
     * @return string complete HTML form
     */
    private function authorizationForm()
    {
        ob_start();
        include('../web/views/authorize.php');
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
        include('../web/views/error.php');
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
        $required = [
            'client_id' => null,
            'redirect_uri' => null,
            'state' => null,
            'response_type' => null
        ];

        // Strip all non-required keys
        $parameters = array_intersect_key($parameters, $required);

        // Strip all FALSE values
        $parameters = array_filter($parameters);

        return (sizeof($parameters) === sizeof($required));
    }
}
