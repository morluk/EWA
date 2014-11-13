/**
 * MouseOver Effekte für index.html und kunde.html
 */
function Mouseover(Schaltflaeche) {
	"use strict";
	Schaltflaeche.className = "ButtonOver";
	// Sonderfall, weil class ein reserviertes Wort ist
}

function Mouseout(Schaltflaeche) {
	"use strict";
	Schaltflaeche.className = "ButtonNormal";
}

function Mousedown(Schaltflaeche) {
	"use strict";
	Schaltflaeche.className = "ButtonDown";
}

function refresh() {
	"use strict";
	location.reload(true);
}