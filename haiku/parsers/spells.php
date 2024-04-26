<?php

// Include common functions
require_once( 'includes/functions.php' );

// Try to load my dump of the families pages
$handle = fopen("source/spells.wiki", "r");

// Set up a placeholder for our 
$spells = [];

// Ensure we loaded the file
if ( $handle ) {

     // RingTemp Variabless
     $ring = '';
     $rank = -1;
     $name = '';
     $source = '';
     $mahoRing = '';

     $ringArray = [];
     $spell = [];
     
	// Read the next line in the file
	while ( ( $line = fgets( $handle )) !== false ) {

		// Check if this is a line denoting a new spell ring
		if( strpos( $line, '+-+' ) !== false ) {

               // Check if we have a spell from the last ring
               if( count( $spell ) > 0 ) {

                    // Put this spell on the array
                    $spells[ $ring ][ $rank ][] = $spell;

                    // Reset the array
                    $spell = [];
               }

			// Remove the leading markup
			$ring = trim( substr( $line, 3 ) );

               // Is this line denoting the universal multielement spells?
               if( $ring == 'The Six Universal Multielement Spells' ) {

                    // We'll sort this out in a better fashion in later
                    $ring = 'Mutltielement-Universal';
               
               // is this the line for specialized multielement spells?
               } else if ( $ring == 'Advanced Multi-Element Spells' ) {

                    // We'll sort this out in a better fashion in later
                    $ring = 'Multielement-Advanced';
               }

               // Set up the spells array for this ring
               $spells[ $ring ] = [];

		// New mastery level
          } else if( strpos( $line, '+ Mastery Level' ) !== false ) {

               // Figure out the rank of these spells
               $rank = trim( str_replace( '+ Mastery Level', '', $line ) );

               // Set up the mastery levels for this rank
               $spells[ $ring ][ $rank ] = [];

          // Spell name
          } else if( strpos( $line, '**' ) !== false ) {

               // Is there already a spell parsed?
               if( count( $spell ) > 0 ) {

                    // Put this spell on the array
                    $spells[ $ring ][ $rank ][] = $spell;

                    // Reset the array
                    $spell = [
                         'description' => [],
                    ];
               }

               // Find out where the source starts
               $sourceStart = strpos( $line, '[' );

               // Does this line have a source?
               if( $sourceStart !== false ) {

                    // Pull out the name
                    $spell[ 'name' ] = trim( substr( $line, 2, $sourceStart - 3 ) );

                    // Pull out the source
                    $spell[ 'source' ] = trim( str_replace( [ '[', ']', ' ', '*' ], '', substr( $line, $sourceStart ) ) );

               // No source
               } else {

                    // Pull out the name
                    $spell[ 'name' ] = trim( str_replace( [ '*' ], '', $line ) );
               }


          // Ring and mastery line
          } else if( strpos( $line, 'Ring/Mastery:' ) !== false ) {

               // Pull out the ring, rank, and keywords
               $line = substr( parseLine( $line, 'Ring/Mastery' ), 0 );

               // Maho needs to be handled differently
               if( $ring == 'Maho' ) {
               
                    // Pull out the maho ring
                    $spell[ 'ring' ] = substr( $line, 0, strpos( $line, ' ' ) );

                    // Remove the ring from the line
                    $line = str_replace( $spell[ 'ring' ], '', $line );

               // Is this a multi-element spell?
               } else if( strpos( $line, '/' ) ) {
                   
                    // Pull out the rings
                    $rings =  substr( $line, 0, strpos( $line, ' ' ) );

                    // Remove the rings from the line
                    $line = str_replace( $rings, '', $line );

                    // The rings for multielement spells will be seperated by slashes
                    $spell[ 'rings' ] = explode( '/', $rings );

                    // Figure out the rank for this spell
                    $rank = trim( substr( $line, 0, 2 ) );

               // Just a regular spell
               } else {  

                    // Strip out the ring and some other nonsense
                    $line = str_replace( [ $ring, 'All', 'Any non-Void' ], '', $line );
                    
               }

               // Pull out the spell rank (which we probbly don't need)
               $spellRank = trim( substr( $line, 0, 2 ) );

               // Just some error checking
               if( $spellRank != $rank ) {

                    // Spit out some debugging text
                    echo "ERROR: " . $spell[ 'name' ] . " has a disagreement between spellRank: $spellRank and rank: $rank\n";
               }


               // Does this spell have any keywords?
               if( strpos( $line, '(') !== false ) {

                    // Pull out the keywords
                    $line = trim( substr( $line, strpos( $line, '(') ), '()' );

                    // Explode the string
                    $keywords = explode( ',', $line );

                    // Set up an array to hold the keywords
                    $spell[ 'keywords' ] = [];

                    // Loop through the exploded array
                    foreach( $keywords as $keyword ) {

                         // Push each keyword onto the spell array
                         $spell[ 'keywords' ][] = trim( $keyword );
                    }

               }

          // Range line
          } else if( strpos( $line, 'Range:' ) !== false ) {

               // Pull out the range
               $spell[ 'range' ] = parseLine( $line, 'Range' );

          // AoE line
          } else if( strpos( $line, 'Area of Effect:' ) !== false ) {

               // Pull out the area of effect
               $spell[ 'aoe' ] = parseLine( $line, 'Area of Effect' );

          } else if( strpos( $line, 'Duration:' ) !== false ) {

               $spell[ 'duration' ] = parseLine( $line, 'Duration' );
          
          // The raises line
          } else if( strpos( $line, 'Raises:' ) !== false ) {

               // Parse our line
               $raises = parseLine( $line, 'Raises' );

               // Blow up the string on ,
               $raises = explode( ',', $raises );

               $spell[ 'raises' ] = [];

               // Loop through the raises
               foreach( $raises as $raise ) {

                    // Push the raise onto the array and remove any extra whitespace
                    $spell[ 'raises' ][] = trim( $raise );                   
               }
          
          // Some spells are special
          } else if( strpos( $line, 'Special:' ) !== false ) {

               // Put it on the spell array
               $spell[ 'special' ] = parseLine( $line, 'Special' );
          
          // Just some debugging
          } else if( strpos( $line, ':' ) !== false && strpos( $line, '*' ) !== false ) {
               echo "Possible Error - Line should of been parsed already: $line\n";

          // If we got here, it's the spells description
          } else {

               // Clean up any whitespace
               $line = trim( $line );

               // Is there anything left?
               if( strlen( $line ) > 0 ) {

                    // Put it on the description array
                    $spell[ 'description' ][] = trim( $line );

               }
          }
     }

     // Going to be one last spell to push onto the array
     $spells[ $ring ][ $rank ][] = $spell;

     // Write our json file
	file_put_contents( '../web/modules/custom/last_haiku_import/json/spells.json', json_encode( $spells ) );
}

// Figure out how many total spells we had
$totalSpells = 0;
$spellCounts = [];

foreach( $spells as $name => $ring ) {

     $ringCount = 0;

     foreach( $ring as $mastery ) {
          $totalSpells += count( $mastery );
          $ringCount += count( $mastery );
     }

     $spellCounts[ $name ] = $ringCount;
}

echo colorize([
     [ 
          'string' => 'Parsing file: ',
          'color' => '',
     ],
     [ 
          'string' => "spells.wiki\n",
          'color' => 'brown',
     ],

     [
          'string' => "     \u{029F} Parsed ",
          'color' => '',
     ],
     [
          'string' => "$totalSpells spells",
          'color' => 'yellow',
     ],
     [    
          'string' => " (",
          'color' => '',
     ],
     [    
          'string' =>  $spellCounts[ 'Universal' ] . " universal",
          'color' => 'green',
     ],
     [    
          'string' => ", ",
          'color' => '',
     ],
     [    
          'string' =>  $spellCounts[ 'Air' ] . " air",
          'color' => 'white',
     ],
     [    
          'string' => ", ",
          'color' => '',
     ],
     [    
          'string' =>  $spellCounts[ 'Earth' ] . " earth",
          'color' => 'brown',
     ],
     [    
          'string' => ", ",
          'color' => '',
     ],
     [    
          'string' =>  $spellCounts[ 'Fire' ] . " fire",
          'color' => 'bold_red',
     ],
     [    
          'string' => ", ",
          'color' => '',
     ],
     [    
          'string' =>  $spellCounts[ 'Water' ] . " water",
          'color' => 'bold_blue',
     ],
     [    
          'string' => ", ",
          'color' => '',
     ],     
     [    
          'string' =>  $spellCounts[ 'Maho' ] . " maho",
          'color' => 'dark_gray',
     ],
     [    
          'string' => ", ",
          'color' => '',
     ],
     [    
          'string' =>  ( $spellCounts[ 'Mutltielement-Universal' ] + $spellCounts[ 'Multielement-Advanced'] ) . " ",
          'color' => 'bold_red',
     ],  
     [    
          'string' => "m",
          'color' => 'bold_green',
     ],
     [    
          'string' => "u",
          'color' => 'yellow',
     ],
     [    
          'string' => "l",
          'color' => 'bold_blue',
     ],
     [    
          'string' => "t",
          'color' => 'bold_purple',
     ],
     [    
          'string' => "i",
          'color' => 'bold_cyan',
     ],
     [    
          'string' => "-",
          'color' => 'white',
     ],
     [    
          'string' => "e",
          'color' => 'dark_gray',
     ],
     [    
          'string' => "l",
          'color' => 'red',
     ],
     [    
          'string' => "e",
          'color' => 'green',
     ],
     [    
          'string' => "m",
          'color' => 'brown',
     ],
     [    
          'string' => "e",
          'color' => 'blue',
     ],
     [    
          'string' => "n",
          'color' => 'purple',
     ],
     [    
          'string' => "t",
          'color' => 'cyan',
     ],
     [    
          'string' => ").\n",
          'color' => '',
     ],
     [    
          'string' => "     \u{029F} Saved to ",
          'color' => '',
     ],          
     [
          'string' => "../web/modules/custom/last_haiku_import/json/spells.json",
          'color' => 'dark_gray',
     ],
     [    
          'string' => ".\n\n",
          'color' => '',
     ],          
]);

?>