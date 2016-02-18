# getRepos
<h1>A testing app for printing basic information about requested user's GitHub repositories.</h1>

<p>The app has been tested on <strong>WampServer Version 2.5. </strong> (PHP 5.5.12, Apache 2.4.9., MySQL 5.6.17).</p>
<p>After extracting the project in desired directory, create the necessary DB with the script located in the script.txt file.
Don't forget to edit the DB connection data and your GitHub name in the config.ini file.</p>
<p>In case of using and inserting a hash of another password in the DB, this needs to be hashed using the password_hash() php function.
Default user and password in the deleting section are <strong>'user'</strong> and <strong>'heslo'</strong> (corresponding to 'pass' table in the DB).</p>
<p>The app uses a basic user authentication against GitHub API, so only 60 requests per hour are supported.</p>
