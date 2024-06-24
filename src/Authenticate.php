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
    public function processAuthentication()
    {
        /*
         * Step 0: setup and process
         */
        session_start();
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();

        /*
         * Step 1: validate the request is valid and complete
         */

        // Show an error if required parameters are missing
        if (! $this->validateParameters($_GET)) {
            return $this->displayError('Required parameters missing');
        }

        // Show an error if the request is invalid
        if (!$this->app->server->validateAuthorizeRequest($request, $response)) {
            return $this->displayError("Parameters are invalid {$response.getStatusText()} {$response.getParameter('error')} {$response.getParameter('error_description')}");
        }

        /*
         * Step 2a: output the correct form
         */
        if (empty($_POST)) {
            return $this->showAuthorisationForm();
        }

        /*
         * Step 2b: handle the submission of the form
         */
        if (!empty($_POST)) {

            // Process logouts
            if (isset($_POST['logout'])) {
                unset($_SESSION['user_id']);
                return $this->showAuthorisationForm();
            }

            // Determine if the user authorised the application
            $authorization = ($_POST['authorization'] == '1');

            // Process a negative response
            if (! $authorization) {
                $this->app->server->handleAuthorizeRequest($request, $response, $authorization);
                return $this->returnToken($response);
            }

            // A positive affirmation requires a valid login
            if (!$this->loggedIn()) {
                // Allow for leading and trailing spaces in usernames to support mobile use cases
                $username = trim($_POST['username']);
                $password = $_POST['password'];

                if (!$this->login($username, $password)) {
                    return $this->showAuthorisationForm('Gebruikersnaam en/of wachtwoord niet correct');
                }
            }

            // Process positive affirmations
            $this->app->server->handleAuthorizeRequest($request, $response, $authorization, $_SESSION['user_id']);
            return $this->returnToken($response);
        }
    }

    /**
     * Login a user
     * @param  string  $username the username given
     * @param  string  $password the password used
     * @return boolean           whether the attempt succeeded
     */
    private function login($username, $password)
    {
        $ldap = LdapHelper::Connect();
        if(@$ldap->bind($username, $password)) {
            $_SESSION['user_id'] = strtolower(trim($_POST['username']));
            return true;
        }
        return false;
    }

    /**
     * Print the authorisation form
     * @param  string $error        optional error to diplay
     */
    private function showAuthorisationForm($error = null)
    {
        // Get the current user
        if (isset($_SESSION['user_id'])) {
            $current_user = $_SESSION['user_id'];
        }
        else {
            $current_user = null;
        }

        // Output form
        ob_start();
        include('../web/views/authorisation_form.php');
        return ob_get_clean();
    }

    /**
     * Whether we have a current user loggedin
     * @return boolean true if logged in, false otherwise
     */
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
     * The required keys are client_id, response_type, redirect_ur and state
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
