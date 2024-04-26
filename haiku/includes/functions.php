<?php

require_once( 'color.php' );

use bart\EscapeColors;

function allColors() {
     $color = new EscapeColors;

     $color->all_fg();
}


// So I don't have to include the class in every file
function colorize( $string ) {
     $color = new EscapeColors;

     return $color->outputString( $string );
}



// The inconsistency finally got to me, time to simplfy this
function parseLine( $line, $word ) {

	// Look at this consistency!
	return trim( str_ireplace( [ "* **$word:**", "* **$word:", "* $word:", "**$word:**", "*  $word:" ], '', $line ) );

}
