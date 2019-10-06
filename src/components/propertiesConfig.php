<?php
/**********************/
/*   DATABASE DATA    */
/**********************/
define("DBUSER", "CENSORED");
define("DBPASS", "CENSORED");
define("DBHOST", "CENSORED");
define("DBNAME", "CENSORED");
define("DBPORT", "CENSORED");
define("DBNAMEDEV", "");
define("DBHOSTDEV", "");

// USERS' ROLES
define('POWERADMIN', 2);
define('POWERGUEST', 1);

/**********************/
/* REGULAR EXPRESSION */
/**********************/

define("REG_EMAIL", '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/');
define("REG_NAME", '/([A-Z]{1}[a-zA-Z\W\s]{2,})/');
// Format Username: Every word character. Symbol like @ $ (non-word character) not allowed
define("REG_USERNAME", '/[a-zA-Z0-9\w]{3,40}/');
// Format Password: Uppercase, Lowercase, Number from 8 to 128 chars
define("REG_PASSWORD", '/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,128}/');
// Format SHA2-512
define("REG_SHA2_512", '/^\w{128}$/');
// Format Date: 2000-02-29 12:12:10
define("MYSQLREG_DATE", '/^((1[6789]|[2-9][0-9])[0-9]{2}-(0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))(\s)(([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])|(24:00:00))?$|^((1[6789]|[2-9][0-9])[0-9]{2}-(0[469]|11)-(0[1-9]|[12][0-9]|30))(\s)(([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])|(24:00:00))?$|^(((16|[248][048]|[3579][26])00)|(1[6789]|[2-9][0-9])(0[48]|[13579][26]|[2468][048]))-02-(0[1-9]|1[0-9]|2[0-9])(\s)(([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])|(24:00:00))?$|^(1[6789]|[2-9][0-9])[0-9]{2}-02-(0[1-9]|1[0-9]|2[0-8])(\s)(([0-1][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])|(24:00:00))?$/');
define("REG_DATE", '/^(19|20)\d\d([- \/.])(0[1-9]|1[012])\2(0[1-9]|[12][0-9]|3[01])$/');
// Format Title: Lettere, Numeri virgola, punto e spazio
define("REG_TITLE", '/[a-zA-Z0-9\ \.\,]{3,40}/');
define("REG_SORTMODE", '/(ASC|DESC|asc|desc)/');
define("REG_CF", '/^[A-Za-z]{6}[0-9]{2}[A-Za-z]{1}[0-9]{2}[A-Za-z]{1}[0-9]{3}[A-Za-z]{1}$/');
define("REG_CFUPPER", '/[A-Z]{6}[0-9LMNPQRSTUV]{2}[A-Z]{1}[0-7LMNPQRST]{1}[0-9LMNPQRSTUV]{1}[A-Z]{1}[0-9LMNPQRSTUV]{3}[A-Z]{1}/');
define("REG_CAP", '/[0-9]{5}/');
define("REG_PIVA", '/[0-9]{11}/');
// Format Address: Via G.D'Annunzio, 33/34b; 46 Brandon Rd. Milton (MA)
define("REG_ADDRESS", "/[a-zA-Z0-9_,'\s\/\.\(\)]{5,50}/");
// Format Tel: +39 081 123 45 67
define("REG_TEL", "/[+0-9\s]{5,20}/");
define("REG_TEL2","/(\+){0,1}([0-9\s]){5,20}/");
// Format IBAN: IT88H0101014900100000010101 (No spaces accepted)
define("REG_IBAN", "/[IT]{2}[0-9]{2}[a-zA-Z]{1}[0-9]{22}/");
define("REG_DIR", "/(ASC|DESC)/");
define("REG_MIME", "/^(image\/)(?:jpe?g|png|gif)$/");
define("REG_PDFMIME", "/application\/pdf/");
