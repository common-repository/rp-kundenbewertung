(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	*/

})( jQuery );

/*
 ' ask user 'delete answer?' by click on the button
*/
function checkDelete($sid) {

  if (confirm("Soll der Datensatz wirklich entfernt werden?")) {

  	//load site and delete
  	document.getElementById("formDelete_" + $sid).submit();

  }
  else {

	// Do nothing

  }

}
