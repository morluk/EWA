<?php
// UTF-8 marker äöüÄÖÜß€
/**
 * Class PageTemplate for the exercises of the EWA lecture
 * Demonstrates use of PHP including class and OO.
 * Implements Zend coding standards.
 * Generate documentation with Doxygen or phpdoc
 *
 * PHP Version 5
 *
 * @category File
 * @package Pizzaservice
 * @author Bernhard Kreling, <b.kreling@fbi.h-da.de>
 * @author Ralf Hahn, <ralf.hahn@h-da.de>
 * @license http://www.h-da.de none
 *          @Release 1.2
 * @link http://www.fbi.h-da.de
 */

// to do: change name 'PageTemplate' throughout this file
require_once './Page.php';

/**
 * This is a template for top level classes, which represent
 * a complete web page and which are called directly by the user.
 * Usually there will only be a single instance of such a class.
 * The name of the template is supposed
 * to be replaced by the name of the specific HTML page e.g. baker.
 * The order of methods might correspond to the order of thinking
 * during implementation.
 *
 * @author Bernhard Kreling, <b.kreling@fbi.h-da.de>
 * @author Ralf Hahn, <ralf.hahn@h-da.de>
 */
class PageTemplate extends Page {
	private $pizzaNameArray = array ();
	private $pizzaPictureArray = array ();
	private $pizzaPriceArray = array ();
	private $counter;
	
	/**
	 * Instantiates members (to be defined above).
	 *
	 * Calls the constructor of the parent i.e. page class.
	 * So the database connection is established.
	 *
	 * @return none
	 */
	protected function __construct() {
		parent::__construct ();
		// to do: instantiate members representing substructures/blocks
	}
	
	/**
	 * Cleans up what ever is needed.
	 *
	 * Calls the destructor of the parent i.e. page class.
	 * So the database connection is closed.
	 *
	 * @return none
	 */
	protected function __destruct() {
		parent::__destruct ();
	}
	
	/**
	 * Fetch all data that is necessary for later output.
	 * Data is stored in an easily accessible way e.g. as associative array.
	 *
	 * @return none
	 */
	protected function getViewData() {
		$this->pizzaNameArray [0] = "Margherita";
		$this->pizzaPriceArray [0] = "4.5";
		$this->pizzaPictureArray [0] = "images/pizza.jpg";
		
		$this->pizzaNameArray [1] = "Salami";
		$this->pizzaPriceArray [1] = "5.0";
		$this->pizzaPictureArray [1] = "images/pizza.jpg";
		
		$this->pizzaNameArray [2] = "Schinken";
		$this->pizzaPriceArray [2] = "5.5";
		$this->pizzaPictureArray [2] = "images/pizza.jpg";
		
		$this->counter = 3;
	}
	
	/**
	 * First the necessary data is fetched and then the HTML is
	 * assembled for output.
	 * i.e. the header is generated, the content
	 * of the page ("view") is inserted and -if avaialable- the content of
	 * all views contained is generated.
	 * Finally the footer is added.
	 *
	 * @return none
	 */
	protected function generateView() {
		$this->getViewData ();
		$this->generatePageHeader ( 'Bestellung' );
		
		echo '<h1>Bestellung</h1>';
		
		echo <<<EOT
		<article class="speisekarte">
		<table>
			<tr class="hidden">
				<th>Bild</th>
				<th>Beschreibung</th>
				<th>Preis</th>
			</tr>
EOT;
		
		for($i = 0; $i < $this->counter; $i ++) {
			echo '<tr>';
			echo '<td onclick="add(this)" ';
			echo 'data-id="1" data-price="';
			echo $this->pizzaPriceArray[$i];
			echo '" ';
			echo 'data-name="';
			echo $this->pizzaNameArray[$i];
			echo '">';
			
			echo '<img src="';
			echo $this->pizzaPictureArray[$i];
			echo '" ';
			echo 'width="150" ';
			echo 'height="150" ';
			echo 'alt="" ';
			echo 'title="Pizza" />';
			echo '</td>';
			echo '<td>Margherita</td>';
			echo '<td>';
			echo $this->pizzaPriceArray[$i];
			echo ' €</td>';
			echo '</tr>';
		}
		
		echo '</table>';
		echo '</article>';
		
		echo <<<EOT
				<article class="warenkorb">
			<form action="http://www.fbi.h-da.de/cgi-bin/Echo.pl"
				accept-charset="UTF-8" method="post">
				<div class="textbox">
					<select id="selectBox" name="top4[]" size="5" multiple>
						<option>test</option>
					</select>
				</div>
				<p class="bestellung">
					<span id="preis">15,70</span> €
				</p>
				<div class="adress">
					<input type="text" name="kunde" value="" size="30" maxlength="40" />
				</div>
				<div class="button1">
					<input onclick="warenkorb.clear();" type="reset" name="Alle löschen" value="Alle löschen" />
				</div>
				<div class="button2">
					<input onclick="warenkorb.removeSelected()" type="button" name="Auswahl löschen" value="Auswahl löschen" />
					<input onclick="warenkorb.submit()" type="submit" name="Bestellen" value="Bestellen" />
				</div>
			</form>
		</article>
		<!-- Script Einbinden -->
		<script type="text/javascript" src="js/funktionen.js"> </script>
		<noscript>
		<p>Bitte aktivieren Sie JavaScript !</p>
		</noscript>	
EOT;
		
		$this->generatePageFooter ();
	}
	
	/**
	 * Processes the data that comes via GET or POST i.e.
	 * CGI.
	 * If this page is supposed to do something with submitted
	 * data do it here.
	 * If the page contains blocks, delegate processing of the
	 * respective subsets of data to them.
	 *
	 * @return none
	 */
	protected function processReceivedData() {
		parent::processReceivedData ();
		// to do: call processReceivedData() for all members
	}
	
	/**
	 * This main-function has the only purpose to create an instance
	 * of the class and to get all the things going.
	 * I.e. the operations of the class are called to produce
	 * the output of the HTML-file.
	 * The name "main" is no keyword for php. It is just used to
	 * indicate that function as the central starting point.
	 * To make it simpler this is a static function. That is you can simply
	 * call it without first creating an instance of the class.
	 *
	 * @return none
	 */
	public static function main() {
		try {
			$page = new PageTemplate ();
			$page->processReceivedData ();
			$page->generateView ();
		} catch ( Exception $e ) {
			header ( "Content-type: text/plain; charset=UTF-8" );
			echo $e->getMessage ();
		}
	}
}

// This call is starting the creation of the page.
// That is input is processed and output is created.
PageTemplate::main ();

// Zend standard does not like closing php-tag!
// PHP doesn't require the closing tag (it is assumed when the file ends). 
// Not specifying the closing ? >  helps to prevent accidents 
// like additional whitespace which will cause session 
// initialization to fail ("headers already sent"). 
//? >