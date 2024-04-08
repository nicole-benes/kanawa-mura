<?php

// Try to load my dump of the families pages
$handle = fopen("source/kata.wiki", "r");

// Set up a placeholder for our 
$katas = [];

// Ensure we loaded the file
if ( $handle ) {

	// Scope
	$ring = '';
	$kata = [];

	// Read the next line in the file
	while ( ( $line = fgets( $handle )) !== false ) {

		// Check if this is a line denoting a new ring
		if( strpos( $line, '++ ' ) !== false ) {

			// Pull out the ring
			$ring = substr( trim( $line ), 3 );

			// New Kata
		} else if( strpos( $line, '__' ) !== false ) {

			// Do we have a kata to push?
			if( count( $kata ) > 0 ) {

				// Get the name of the kata
				$name = $kata[ 'name' ];

				// Remove the name from the kata array
				unset( $kata[ 'name' ] );

				// Push the kata onto the array
				$katas[ $name ] = $kata;
			
			}					

			// Pull out the name
			$name = substr( $line, 2, -3 );

			// Make a new kata array
			$kata = [
				'name' => $name,
				'rings' => [],
			];

		// Ring / Mastery line
		} else if( strpos( $line, '* Ring/Mastery' ) !== false ) {

			// Clean out the junk
			$rank = parseLine( $line, 'Ring/Mastery' );

			// Try to pull out the number
			$rank = trim( str_replace( $ring, '', $rank ) );

			// Did we get a number?
			if( !is_numeric( $rank ) ) {

				// Try replacing void
				$rank = trim( str_replace( 'Void', '', $rank ) );

				// Still not a number
				if( !is_numeric( $rank ) ) {

					// Easiest to just hard code these
					if( strpos( $rank, '3' ) !== false ) {
						$rank = 3;
					} else if( strpos( $rank, '4' ) !== false ) {
						$rank = 4;
					} else if( strpos( $rank, '5' ) !== false ) {
						$rank = 5;
					} else if( strpos( $rank, '6' ) !== false ) {
						$rank = 6;

					// We shouldn't get here...
					} else {
						
						// Output debugging text
						echo "Invalid rank on: |$ring| -> |$rank|\n";
					}
				}
			}

			// Put the rank onto the kata
			$kata[ 'rank' ] = $rank;

			// Check if we have a complicated ring
			if( strpos( $ring, 'Void and Other' ) !== false ) {

				// Just a bunch of checking rings vs the string
				if( strpos( $line, 'Void' ) !== false ) {
					$kata[ 'rings' ][] = 'Void';
				}

				if( strpos( $line, 'Air' ) !== false ) {
					$kata[ 'rings' ][] = 'Air';
				}

				if( strpos( $line, 'Earth' ) !== false ) {
					$kata[ 'rings' ][] = 'Earth';
				}

				if( strpos( $line, 'Fire' ) !== false ) {
					$kata[ 'rings' ][] = 'Fire';
				}

				if( strpos( $line, 'Water' ) !== false ) {
					$kata[ 'rings' ][] = 'Water';
				}

			// Just one ring
			} else {

				// Push the ring onto the array
				$kata[ 'rings' ][] = $ring;
			}

		// Schools line
		} else if( strpos( $line, '* Schools' ) !== false ) {

			// Clean out the junk
			$line = parseLine( $line, 'Schools' );

			// Can just put the schools onto the array
			$kata[ 'schools' ] = trim( $line );

		// Is this Kata special?
		} else if( strpos( $line, '* Special' ) !== false ) {

			// Clean out the junk
			$line = parseLine( $line, 'Special' );

			// Can just put the special text onto the array
			$kata[ 'special' ] = trim( $line );		
			
		// The effect text
		} else if( strpos( $line, '* Effect' ) !== false ) {

			// Clean up junk
			$line = parseLine( $line, 'Effect' );

			// Put it onto the array
			$kata [ 'effect' ] = trim( $line );

		// Something not covered
		} else {
			
			// Is there something on this line?
			if( strlen( trim( $line ) ) > 0 ) {
				echo "Kata " . $kata[ 'name' ] . " Has extra lines:\n";
				echo $line . "\n";
			}
		}
	}

	// Always that one last thing to push
	$name = $kata[ 'name' ];

	// Remove the name from the kata array
	unset( $kata[ 'name' ] );

	// Push the kata onto the array
	$katas[ $name] = $kata;

	// Write our json file
	file_put_contents( '../web/modules/custom/last_haiku_import/json/kata.json', json_encode( $katas ) );
}

// The inconsistency finally got to me, time to simplfy this
function parseLine( $line, $word ) {

	// Look at this consistency!
	return trim( str_ireplace( [ "* **$word:**", "* **$word:", "* $word:", "**$word:**", "*  $word:" ], '', $line ) );

}