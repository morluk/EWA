<?php	// UTF-8 marker äöüÄÖÜß€

abstract class Page
{
    // --- ATTRIBUTES ---
    
    protected $_database = null;
    
    // --- OPERATIONS ---
    
    protected function __construct() 
    {
    	// activate full error checking
    	error_reporting (E_ALL);
    	
    	// open database
    	require_once 'pwd.php'; // read account data
        $this->_database = @new MySQLi($host, $user, $pwd, "pizzaservice");
        // check connection to database
        if (mysqli_connect_errno())
        	throw new Exception("Keine Verbindung zur Datenbank: ".mysqli_connect_error());
        // set character encoding to UTF-8
        if (!$this->_database->set_charset("utf8"))
        	throw new Exception("Fehler beim Laden des Zeichensatzes UTF-8: ".$this->database->error);
    }
    
    protected function __destruct()    
    {
        //close database
    	$this->_database->close();
    }
    
    protected function generatePageHeader($headline = "", $h1 = "", $skript = "") 
    {
        $headline = htmlspecialchars($headline);
        // define MIME type of response (*before* all HTML):
        header("Content-type: text/html; charset=UTF-8");
        
		// output HTML header
		echo <<<EOT
		<!DOCTYPE html>
		<html>
			<head>
				<meta charset="UTF-8" />
				<link rel="stylesheet" type="text/css" href="default.css" />
				<meta name="description" content="Pizzabestellung" />
				<meta name="author" content="M.Hilberg" />
				<meta name="keywords" content="Pizza,Bestellung" />
				<meta name="robots" content="noindex" />
				<title>$headline</title>
			</head>
			<body $skript>
				<h1>$h1</h1>

EOT;
    }

    protected function generatePageFooter() 
    {
		echo <<<EOT
			</body>
		</html>

EOT;
    }

    protected function processReceivedData() 
    {
        if (get_magic_quotes_gpc()) {
            throw new Exception
                ("Bitte schalten Sie magic_quotes_gpc in php.ini aus!");
        }
    }
}