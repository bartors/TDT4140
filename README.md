1:DATABASE:

If you vant to set up just the web service and connect to our designated database, it is a posibility, however you'll have to connet via an NTNU connection or use a VPN. It is aswell not known how long the service will run on NTNUs servers. Therfore it is higly recommended that you use the SQLcode in DB/DBcode.txt to create your own ClassMateDB.

It occurs there is a problem with creating unique key on MariaDB because of the length. If it occurs, change the length of string to 100 charackers (from varchar(255) to varchar(100)), but remember to change the if statement that check if the string is of valid length in register.php.


2:SECURITY:
Our security system uses a public salt that is stored in PHP, and two random generated private salts which are stored in database. We use aswell a username as forth salt. Given that the project is open source and the method of hasing pasword, and connecting the various parts of a password are open to public and easy accesible any user that wants to deploy its own ClassMate service should change the public salt and change the way a password is made.  
