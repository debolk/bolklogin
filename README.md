bolklogin
=========

OAuth2 endpoint for applications in De Bolk.

Usage
-----
1. Register an application in the `oauth_clients` table:  
   `INSERT INTO oauth_clients (client_id, client_secret, redirect_uri) VALUES ('example', 'examplepass', 'http://example.org/');`
2. Once a user tries to log in, redirect them to:  
   `https://login.i.bolkhuis.nl/authorize?response_type=code&client_id=example&client_pass=examplepass&redirect_uri=http://example.org&state=123456`
3. They will log in and redirect to you with a code: `http://example.org/?code=2a134b08d15a90e5901f24a98a129&state=123456`
4. Using this code, request an access token:  
   POST to: `https://example:examplepass@login.i.bolkhuis.nl/token`  
   with the following data: `grant_type=authorization_code&code=2a134b08d15a90e5901f24a98a129&redirect_uri=http://example.org`
	  -or-
	 POST to: `https://login.i.bolkhuis.nl/token`
   with the following data: `grant_type=authorization_code&code=2a134b08d15a90e5901f24a98a129&redirect_uri=http://example.org&client_id=example&client_secret=examplepass`
5. You can use the resulting access token as a parameter to api servers so they can check if you represent the logged in user
6. You can also check if a user is of a certain class:
    * `https://login.i.bolkhuis.nl/bestuur/?access_token={accesstoken}`  
      Gives a status code of 200 if the user is bestuur or beheer
    * `https://login.i.bolkhuis.nl/lid/?access_token={accesstoken}`  
      Gives a status code of 200 if the user is lid or kandidaad-lid
    * `https://login.i.bolkhuis.nl/bekend/?access_token={accesstoken}`  
      Gives a status code of 200 if the user is lid, kandidaad-lid or oud-lid
    * `https://login.i.bolkhuis.nl/ictcom/?access_token={accesstoken}`
		  Gives a status code of 200 if the user is bestuur, ictcom or beheer`
