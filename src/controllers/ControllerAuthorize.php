<?php

use ControllerBase;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ControllerAuthorize extends ControllerBase {

	public function process_request(Request $request, Response $response, array $args): Response {
		session_start();
		$req = OAuth2\Request::createFromGlobals();
		$res = new \OAuth2\Response();

		$this->response = $response;

		//validate parameters
		if (!$this->validateParameters($_GET)) {
			return $this->displayError('Required paramaters missing');
		}
		//validate the request
		if (!$this->server->validateAuthorizeRequest($req, $res)){
			$error = $res->getParameter('error_description');
			return $this->displayError("Parameters are invalid: <br><br> $error");
		}

		//display auth form
		if (empty($_POST)) {

			return $this->displayAuthForm();

		} else { //handle submission of form

			//process logouts
			if (isset($_POST['logout'])) {
				unset($_SESSION['user_id']);
				unset($_SESSION['user_fullname']);
				return $this->displayAuthForm($response);
			}

			//did the user authorise the application?
			$authorization = ($_POST['authorization'] == '1');

			//process the user not authorizing
			if (!$authorization) {

				$this->server->handleAuthorizeRequest($req, $res, false);
				return $this->returnToken($res);

			} else if (!$this->isLoggedIn()) { //if the user did authorize, we need to process the login

				$username = trim($_POST('username'));
				$password = $_POST['password'];

				if (!$this->loginUser($username, $password)) {
					return $this->displayAuthForm('Username and/or password invalid');
				}

			}

			//process authorization
			$this->server->handleAuthorizeRequest($req, $res, true);
			return $this->returnToken($res);
		}
	}

	/**
	 * Helper function to display a html file
	 *
	 * @param string $htmlFile the html file to display
	 * @return Response
	 */
	private function displayHTML(string $htmlFile) : Response {

		//get contents of provided html file and write to response
		//start output buffering
		ob_start();

		//include the php file and execute code within
		include($htmlFile);

		//get content from the output buffer and save it in $html
		$html = ob_get_clean();

		//write content to Response with text/html content-type
		return ResponseHelper::data($this->response, $html, 'text/html');
	}

	/**
	 * Helper function to display php errors
	 *
	 * @param string $error_message the error message
	 * @return Response
	 */
	private function displayError(string $error_message): Response {
		return $this->displayHTML( '../web/views/error.php');
	}

	/**
	 * Helper function to display the authorisation form
	 *
	 * @param string|null $error if applicable, the error to display from an earlier attempt
	 * @return Response
	 */
	private function displayAuthForm(string $error = null): Response {
		$user = null;
		$user_fullname = null;
		if ($this->isLoggedIn()){
			$user = $_SESSION['user_id'];
			$user_fullname = $_SESSION['user_fullname'];
		}

		return $this->displayHTML('../web/views/authorisation_form.php');
	}

	/**
	 * Helper function to convert the OAuth2\Response to a Slim\Response
	 *
	 * @param \OAuth2\Response $res the OAuth2 response
	 * @return Response the converted Slim response
	 */
	private function returnToken(OAuth2\Response $res) : Response{
		return ResponseHelper::convertFromOAuth($res);
	}

	/**
	 * Validates whether all four expected parameters are present in an array
	 * The required keys are client_id, response_type, redirect_ur and state
	 * Extra given parameters are ignored
	 * @param array $params
	 * @return boolean
	 */
	private function validateParameters(array $params) : bool {
		$required = [
			'client_id' => null,
			'redirect_uri' => null,
			'state' => null,
			'response_type' => null
		];

		//strip non-required keys
		$params = array_intersect_key($params, $required);

		//strip FALSE values
		$params = array_filter($params);

		return sizeof($params) === sizeof($required);
	}

	/**
	 * Function to check whether a logged in user is present
	 * @return bool true if logged in, else false
	 */
	private function isLoggedIn() : bool {
		return isset($_SESSION['user_id']);
	}

	/**
	 * logs in a user
	 *
	 * @param string $username
	 * @param string $password
	 * @return bool  true if logged in, else false
	 */
	private function loginUser(string $username, string $password) {
		$ldap = LdapHelper::Connect();

		if($ldap->bind($username, $password)) {
			$_SESSION['user_id'] = strtolower(trim($username));
			$_SESSION['user_fullname'] = $ldap->getName($username);
			return true;
		}
		return false;
	}

}