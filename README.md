1:DATABASE:

It occurs there is a problem with creating unique key on MariaDB because of the length. If it occurs, change the length of string to 100 charackers (from varchar(255) to varchar(100)), but remember to change the if statement that check if the string is of valid length in register.php.
