# Registration & Login - PHP & OOP

This is a Registration & Login application made in PHP and OOP. A live demo of this webapplication can be found here:
https://www.nswardh.com/demo/login/

NOTE: The live demo has been styled and pimped using HTML and CSS, other than that, its all the same and use the same files and objects as you find here.

# Protection
The application include protection from SQL-injections and Cross-Site-Forgery-Request attacks by binding the SQL-queries to PDO and using cookies with the "same-site" attribute for XSFR-protection.

# Password
In this application I chosed not to verify the passwords other than password length. Imo developers shouldn't restrict users from freely choosing their own passwords by telling them what characters they can and can't use. Also, they shouldn't force the users to add letters and symbols etc. Instead, inform the user if the password is weak and what they can do about it to improve the strength (this feature is not included in this application). Maybe add some capital letters, numbers, a symbol, increase the length etc but don't force them. Inform them and then let the user decide. By limiting passwords to a smaller char-set will just make it easier for the attacker to break the password since less work and time has to be spent breaking/brute forcing it.
Since I ask the user to select a password and then to verify it again, then I know the user added those spaces and symbols intentionally.

If you wish to verify and set up a strict "traditional" password verification, then you can edit the "Validate" class and add your password verification-code to the "ValidatePass()" method.

# Installation
1) Edit ini/config.ini file and enter your database connection info
2) Chmode the "init" folder to protect your database-information from leaking out!
3) Import "Database_Structure.sql" in your database to create the needed tables and columns
