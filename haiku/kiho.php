<?php

// Try to load my dump of the families pages
$handle = fopen("source/kiho.wiki", "r");

// Set up a placeholder for our 
$kihos = [];

// Ensure we loaded the file
if ( $handle ) {

	// Scope
	$ring = '';
	$kiho = [];

	// Read the next line in the file
	while ( ( $line = fgets( $handle )) !== false ) {

		// Check if this is a line denoting a new ring
		if( strpos( $line, '++ ' ) !== false ) {

			// Pull out the ring
			$ring = substr( trim( str_replace( 'Kiho', '', $line ) ), 3 );

		// New Kata
		} else if( strpos( $line, '__' ) !== false ) {

			// Do we have a kata to push?
			if( count( $kiho ) > 0 ) {

				// Get the name of the kata
				$name = $kiho[ 'name' ];

				// Remove the name from the kata array
				unset( $kiho[ 'name' ] );

				// Push the kata onto the array
				$kihos[ $name ] = $kiho;
			
			}					

			// Pull out the name
			$name = substr( $line, 2, -3 );

			// Make a new kata array
			$kiho = [
				'name' => $name,
				'ring' => $ring,
			];

			// Ring / Mastery line
		} else if( strpos( $line, '* Ring/Mastery' ) !== false ) {

			// Clean out the junk
			$rank = parseLine( $line, 'Ring/Mastery' );

			// Try to pull out the number
			$rank = trim( str_replace( $ring, '', $rank ) );

			// We got an error
			if( !is_numeric( $rank ) ) {

				// Debugging text
				echo "Invalid rank in " . $kiho[ 'name' ] . ": $line\n";
			}

			// Put the rank onto the kata
			$kiho[ 'rank' ] = $rank;

		// Type line
		} else if( strpos( $line, '* Type' ) !== false ) {

			// Clean out the junk
			$line = parseLine( $line, 'Type' );

			// Is this a type with a modifier?
			if( strpos( $line, '(' ) !== false ) {

				// Pull out the type
				$kiho[ 'type' ] = trim( substr( $line, 0, strpos( $line, '(' ) ) );
				
				// Remove the type from the line
				$modifiers = str_replace( $kiho[ 'type' ], '', $line );

				// Remove the ( and )
				$modifiers = trim( $modifiers, '() ');

				// Blow up the string on a ,
				$modifiers = explode( ',', $modifiers );

				// Set up an array for our keywords
				$kiho[ 'keywords' ] = [];

				// Go through each of the parts
				foreach( $modifiers as $mod ) {

					// Push that part onto the keywords array
					$kiho[ 'keywords' ][] = trim( $mod );
				}
			
			// Nothing special about this type
			} else {
				$kiho[ 'type' ] = $line;
			}

		// Something not covered
		} else {
			
			// Is there something on this line?
			if( strlen( trim( $line ) ) > 0 ) {
				$kiho[ 'description' ] = trim( $line );
			}
		}
	}

	// Always that one last thing to push
	$name = $kiho[ 'name' ];

	// Remove the name from the kata array
	unset( $kiho[ 'name' ] );

	// Push the kata onto the array
	$kihos[ $name] = $kiho;


	foreach( $kihos as $name => $kiho ) {
		if( !isset( $kiho[ 'rank' ] ) ) {
			echo "$name\n";
		}
	}

	// Write our json file
	file_put_contents( '../web/modules/custom/last_haiku_import/json/kiho.json', json_encode( $kihos ) );
}

// The inconsistency finally got to me, time to simplfy this
function parseLine( $line, $word ) {

	// Look at this consistency!
	return trim( str_ireplace( [ "* **$word:**", "* **$word:", "* $word:", "**$word:**", "*  $word:" ], '', $line ) );

}