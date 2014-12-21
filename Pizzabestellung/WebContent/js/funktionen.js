/**
 * Funktionen für Warenkorb in bestellung.html
 */

// Klasse Warenkorb
function Warenkorb(idSelect, idPrice) {
	"use strict";
	// private member
	var m_nodeSelect = idSelect,
		m_nodePrice = idPrice,
		itemArray = [],
		totalPrice = 0.0;

	// methoden
	// private: erneuert textbox und preis
	var updateView = function() {
		var liste = document.getElementById(m_nodeSelect);
		// Liste loeschen
		while (liste.firstChild !== null) {
			liste.removeChild(liste.firstChild);
		}
		// Liste fuellen
		var i = 0,
			neuesElement,
			neuerText;
		for (i = 0; i < itemArray.length; i++) {
			neuesElement = document.createElement("option");
			neuerText = document.createTextNode(itemArray[i].name);
			neuesElement.appendChild(neuerText);
			liste.appendChild(neuesElement);
		}
		// Preis aktualisieren
		var preisTag = document.getElementById(m_nodePrice);
		preisTag.innerHTML = totalPrice.toFixed(2);
	};
	// private: löscht item mit name aus liste
	var remove = function(name) {
		var i = 0;
		for (i=0;i<itemArray.length;i++) {
			if (itemArray[i].name === name) {
				totalPrice -= itemArray[i].price;
				itemArray.splice(i, 1);
				break;
			}		
		}
	};
	// public: fügt item zu item liste hinzu
	this.add = function(new_price, new_name) {
		var new_item = {name: new_name, price: Number(new_price)};
		itemArray[itemArray.length] = new_item;
		totalPrice += Number(new_price);
		updateView();
	};
	// public: löscht gesamten Warenkorb
	this.clear = function() {
		itemArray.splice(0, itemArray.length);
		totalPrice = 0.0;
		updateView();
	};
	// public: löscht selektierte Items
	this.removeSelected = function() {
		var liste = document.getElementById(m_nodeSelect),
			i = 0,
			name;
		for (i=0; i<liste.options.length; i++) {
			if (liste.options[i].selected) {
				name = liste.options[i].text;
				remove(name);
			}
		}
		updateView();
	};
	// public: selektiert alle items bevor Warenkorb abgeschickt wird
	this.selectAll = function() {
		var options = document.getElementById(m_nodeSelect).options,
			i = 0;
		for (i=0; i<options.length; ++i) {
			options[i].selected = "1";
		}
	};
}

/**
 * Funktionsfluss
 */
var warenkorb;

// onload="init()"
function init() {
	"use strict";
	warenkorb = new Warenkorb("selectBox", "preis");
	warenkorb.clear();	//wg. UpdateView fuer Preis
}
// bei click auf pizza in Speisekarte
function add(node) {
	"use strict";
	var price = node.getAttribute("data-price"),
		name = node.getAttribute("data-name");
	warenkorb.add(price, name);
}
