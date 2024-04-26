<?php

// Include common functions
require_once( 'includes/functions.php' );

// Try to load my dump of the families pages
$handle = fopen("source/families.wiki", "r");

// Set up an array to hold the clans and families
$familyArray = [];

// 1, 2, 3, counting!
$clanCount = 0;
$familyCount = 0;

// Ensure we loaded the file
if ( $handle ) {

	// Variable to hold whatever we're working on
	$clanArray = [];
	$memberFamilies = [];

	// Variable to hold the parent of this family
	$parent = false;;
	$clan = false;
	$family = false;;

	// Read the next line in the file
	while ( ( $line = fgets( $handle )) !== false ) {

		// Check if this is a line denoting a new grouping
		if( strpos( $line, '+!+' ) !== false ) {

			// Check if we have a family to add before we move onto the next major grouping
			if( count( $clanArray ) > 0 ) {
	
				// Push this family onto the appropriate clan and parent grouping
				$familyArray[ $parent ][ 'clans' ] = $clanArray;

				// Empty the clan array
				$clanArray = [];

				// Empty the families array
				$memberFamilies = [];
			}

			// Reset what clan and family we're using
			$clan = false;
			$family = false;

			// Pull out the large grouping name
			$parent = trim( substr( $line, 3 ) );

               // Increase our clan count unless it's one of the major groupings
               if( $parent !== 'Great Clans' || $parent !== 'Minor Clans' || $parent !== 'Lost Clans' ) {
                    $clanCount++;
               }

		// Check if this is a line denoting a new clan
		} else if( strpos( $line, '++' ) !== false ) {

			// Is there a member family to push onto the array?
			if( count( $memberFamilies ) > 0 ) {

				// Push it
				$clanArray[ $clan ][ 'families' ][ $family ] = $memberFamilies;

				// Reset that we have a family
				$family = false;
			}

			// Remove the leading markup
			$clan = substr( $line, 2 );

			// Remove any pesky whitespace
			$clan = trim( $clan );
	
			// Empty the clan array
			$memberFamilies = [];

               // Increase our clan count
               $clanCount++;

		// Check if this is a line denoting the family
		} else if( strpos( $line, '+ __' ) !== false ) {
			
			// Account for ronin and imperial families
			if( !$clan ) {
				$clanArray = [];
				$memberFamilies = [];
				$clan = 'Not Applicable';
			}

			if( count( $memberFamilies ) > 0 ) {

				$clanArray[ $clan ][ 'families' ][ $family ] = $memberFamilies;
				
				$memberFamilies = [];
			}

			// Remove all the extra whitespace
			$line = trim( $line );

               // Placeholder for source
               $source = false;

               // Check for a source
               if( strpos( $line, '[' ) !== false ) {

                    // Pull out the source
                    $source = substr( $line, strpos( $line, '[' ) + 1, strpos( $line, ']' ) - strpos( $line, '[' ) - 1 );

                    $line = trim( substr( $line, 0, -(strlen( $source ) ) - 2 ) );

               }

			// Find where the family ends
			$length = strpos( $line, 'Family');

			// Offset for the word family or order or whatever
			$offset = 6;

			// Check if we found family, if not look for order
			if( !$length && strpos( $line, 'Order' ) ) {

				// Find out where order is
				$length = strpos( $line, 'Order' );

				// Offset is 5 for order
				$offset = 5;

			// There's a spider family to mess things up too
			} else if( !$length && strpos( $line, 'Monks' ) ) {

				// Find out where Monks is
				$length = strpos( $line, 'Monks' );

				// Offset is 5 for Monks
				$offset = 5;
			}

			// Pull out the family
			$family = substr( $line, 8, $length - 9 );

               // Get the full family title just incase we need it
               $fullFamily = substr( $line, 4, $length + $offset - 4  );

			// Remove the unnessecary +1
			$stat = substr( $line, $length + 5 + $offset );

			// Remove the trailing underscores
			$stat = trim( $stat, '__' );

			// Put these on the clan array
			$memberFamilies = [
				'benefit' => [
					'trait' => $stat,
					'value' => 1,
				],
                    'full' => $fullFamily,
				'description' => [],
			];

               // Increase our family counter
               $familyCount++;

               // Did we find a source?
               if( $source !== false ) {

                    // Set the source
                     $memberFamilies[ 'source' ] = $source;
               }

		// Either an empty line or the description of the family
		} else {

			// Remove any whitespace on the beginning and end
			$line = trim( $line );

			// Check if there's anything left
			if( strlen( $line ) > 0 ) {

				if( !$clan && !$family ) {

					$familyArray[ $parent ][ 'description' ][] = $line;

				} elseif( !$family && strlen( $parent ) > 0 ) {

					$clanArray[ $clan ][ 'description' ][] = $line;

				} else {
					$memberFamilies[ 'description' ][] = $line;
				}

			}

		}

          if( count( $memberFamilies ) > 0 ) {

               // Push thisthe last family onto the appropriate clan and parent grouping
               $clanArray[ $clan ][ 'families' ][ $family ] = $memberFamilies;
          }
     }

	$familyArray[ $parent ][ 'clans' ] = $clanArray;

	// Close the input file
    fclose( $handle );
}

file_put_contents( '../web/modules/custom/last_haiku_import/json/clans-families.json', json_encode( $familyArray ) );

echo colorize([
     [ 
          'string' => 'Parsing file: ',
          'color' => '',
     ],
     [ 
          'string' => "families.wiki",
          'color' => 'purple',
     ],
     [    
          'string' => ".\n",
          'color' => '',
     ],     
     [
          'string' => "     \u{029F} Parsed ",
          'color' => '',
     ],
     [
          'string' => "$clanCount clans",
          'color' => 'bold_purple',
     ],
     [    
          'string' => " and ",
          'color' => '',
     ],
     [    
          'string' =>  "$familyCount families",
          'color' => 'bold_purple',
     ],
     [    
          'string' => ".\n",
          'color' => '',
     ],
     [    
          'string' => "     \u{029F} Saved to: ",
          'color' => '',
     ],          
     [
          'string' => "../web/modules/custom/last_haiku_import/json/clans-families.json",
          'color' => 'dark_gray',
     ],
     [    
          'string' => ".\n\n",
          'color' => '',
     ],     
]);
?>
