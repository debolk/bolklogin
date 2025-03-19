<?php

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ControllerPassword extends ControllerAuthorize {

	public function process_request(Request $request, Response $response, array $args): Response {
		session_start();
		$req = OAuth2\Request::createFromGlobals();
		$res = new \OAuth2\Response();

		$this->response = $response;

		$_GET = array_merge($_GET, array('client_id' => 'bolklogin'));

		//display pass form
		if (!isset($_POST['change_pass'])) {

			if (!$this->isLoggedIn()
				and isset($_POST['authorization']) and $_POST['authorization'] == '1') { //if the user did authorize, we need to process the login

				$username = trim($_POST['username']);
				$password = $_POST['password'];

				if (!$this->loginUser($username, $password)) {
					return $this->displayAuthForm('Username and/or password invalid');
				}
			}

			return $this->displayPassForm();

		} else { //handle submission of form

			//did the user authorise the application?
			$change_pass = ($_POST['change_pass'] == '1');

			if (isset($_SESSION['user_id'])) {
				$user = $_SESSION['user_id'];
			} else if (isset($_GET['user_id'])){
				$user = $_GET['user_id'];
			} else {
				return $this->displayAuthForm('No username found, please log in');
			}

			//process the user changing password
			if ($change_pass and $this->isLoggedIn()) {
				$password_old = $_POST['password_old'];
				$password_new = $_POST['password_new'];
				$password_new_confirm = $_POST['password_new_confirm'];

				$ldap = LdapHelper::Connect();

				if ($password_new_confirm !== $password_new) {
					return $this->displayPassForm("New passwords don't match.");
				} else if ($ldap->set_password($user, $password_old, $password_new)) {
					$this->logout();
					return $this->displayPassForm(msg: "Password changed successfully.");
				} else {
					$err = $ldap->lastErrorNo();
					syslog(LOG_ERR, $ldap->lastError());
					if ($err === 19) {
						return $this->displayPassForm("Error: password cannot match an old password and must be at least 12 characters.");
					} else if ($err == 49) {
						return $this->displayPassForm("Error: Old password doesn't match.");
					} else {
						return $this->displayPassForm("Error: {$ldap->lastError()}. Code {$ldap->lastErrorNo()}");
					}
				}
			} else if (!$change_pass) {

				return $this->logout();

			} else {
				return $this->displayAuthForm();
			}
		}
	}

	/**
	 * Helper function to display the authorisation form
	 *
	 * @param string|null $error if applicable, the error to display from an earlier attempt
	 * @return Response
	 */
	private function displayPassForm(string $error = null, string $msg = null): Response {
		$user = null;
		$user_fullname = null;
		if ($this->isLoggedIn()){
			$user = $_SESSION['user_id'];
			$user_fullname = $_SESSION['user_fullname'];
		} else {
			return $this->displayAuthForm();
		}

		//get contents of provided html file and write to response
		//start output buffering
		ob_start();

		//include the php file and execute code within
		include('../web/views/password_form.php');

		//get content from the output buffer and save it in $html
		$html = ob_get_clean();

		//write content to Response with text/html content-type
		return ResponseHelper::data($this->response, $html, 'text/html');
	}

}