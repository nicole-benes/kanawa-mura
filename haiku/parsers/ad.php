<?php

// Include common functions
require_once( 'includes/functions.php' );

// Array to hold our lines from the file
$rawArray = [
     'advantage' => [],
     'disadvantage' => [],
];

// Load the two files into the array
$rawArray[ 'advantage' ] = load_file( 'advantages' );
$rawArray[ 'disadvantage' ] = load_file( 'disadvantages' );

// Set up scope for our parsed advantages and disadvantages
$parsed = [];

$advantages = 0;
$disadvantages = 0;
// From this point forward, I'm going to just call everything an advantage

// Loop through the array
foreach( $rawArray as $type => $fileRow ) {

     // Scope
     $advantage = [];

     $list = [];
     $table = [];

     // Flags to indicate what we're doing
     $inTable = false;
     $inList = false;

     // Loop through the line in each array
     foreach( $fileRow as $line ) {

          // Is this a new advantage?
          if( strpos( $line, '**' ) !== false ) {

               // Is there an advantage we were working on?
               if( count( $advantage ) > 0 ) {

                    // Just incase...
                    if( count( $table ) > 0 ) {

                         // Put the table into the description
                         $advantage[ 'description' ][] = [
                              'type' => 'table',
                              'table' => $table,
                         ];
                    }

                    // Checking list too
                    if( count( $list ) > 0 ) {

                         // Put this list on the description
                         $advantage[ 'description' ][] = [ 
                              'type' => 'list',
                              'list' => $list,
                         ];
                    }

                    // Push this advantage onto the array
                    $parse[] = $advantage;

                    // Set our flags to be safe
                    $inTable = false;
                    $inList = false;

                    // Reset our element containers
                    $table = [];
                    $list = [];

                    // Reset the advantage array
                    $advantage = [
                         'type' => $type,
                         'description' => [],
                         'subtype' => [],
                    ];
               }

               // Set the type
               $advantage[ 'type' ] = $type;

               if( $type == 'advantage' ) {
                    $advantages++;
               } else {
                    $disadvantages++;
               }

               // Do some cleanup on the line
               $line = substr( trim( $line ), 2, strlen( $line ) - 5 );

               // Chat GPT says this will work
               $pattern = '/^(.*?)\s*(?:\[(.*?)\])?\s*(?:\((.*?)\))?\s*(?:\[(.*?)\])?$/';

               // $matches[1] contains the first text
               // $matches[2] contains the text inside the first set of square brackets (if present)
               // $matches[3] contains the text inside the parentheses (if present)
               // $matches[4] contains the text inside the second set of square brackets (if present)
               preg_match($pattern, $line, $matches);

               // Ensure there's a name
               if( !$matches[ 1 ] ) {
                    echo "Error: $line has no name.\n";
               }

               // There's always a name
               $advantage[ 'name' ] = $matches[ 1 ];

               // Ensure there's a cost
               if( !$matches[ 3 ] ) {
                    echo "Error: $line has no cost.\n";
               }

               // There's always a cost
               $advantage[ 'cost' ] = $matches [ 3 ];

               // Figure out numeric cost
               $advantage[ 'numeric' ] = parseCost( $matches[ 3 ], $matches[ 1 ] );

               // Is there a subtype?
               if( $matches[ 2 ] ) {

                    // Does this advantage have more than one subtype 
                    if( strpos( $matches[ 2 ], '/' ) !== false ) {

                         // Explode it on the delimiter
                         $parts = explode( '/', $matches[ 2 ] );

                         // Loop through each of the parts
                         foreach( $parts as $part ) {

                              // Put the types on the array
                              $advantage[ 'subtype' ][] = trim( $part );
                         }

                    } else {

                         // Set the subtype
                         $advantage[ 'subtype' ][] =  $matches[ 2 ];  
                    }

               }

               // Is there a source?
               if( isset( $matches[ 4 ] ) && $matches [ 4 ] ) {
                    $advantage[ 'source' ] = $matches[ 4 ];
               }

          // Header in the description
          } else if( strpos( $line, '++') !== false ) {

               // Indicate we are in a table
               $inTable = true;

               // Explode the line on ++s to get the table headers
               $parts = explode( '++', $line );

               // Set up our table
               $table[ 'header' ] = [];
               $table[ 'rows' ] = [];

               // Loop thrugh the heaqders
               foreach( $parts as $part ) {

                    // Clean up whitespace
                    $part = trim( $part );

                    // Is there anything left?
                    if( strlen( $part ) > 0 ) {

                         // Put it into the header
                         $table[ 'header' ][] = $part ;
                    }
               }

          // Table row
          } else if( strpos( $line, '||') !== false ) {

               // Are we in a list?
               if( $inList ) {

                    // Not any more!
                    $inList = false;

                    // Put the list on the description
                    $advantage[ 'description' ][] = [ 
                         'type' => 'list',
                         'list' => $list,
                    ];

                    // Reset the list variable
                    $list = [];
               }

               // Are in a table? 
               if( !$inTable ) {

                    // Well we are now
                    $inTable = true;

                    // Set up the table array
                    $table[ 'rows' ] = [];
               }

               // Array to hold our row
               $row = [];

               // Explode the line on ||s
               $parts = explode( '||', $line );

               // Loop through the parts
               foreach( $parts as $part ) {

                    // Remove that pesky whitespace
                    $part = trim( $part );

                    // Did we end up with anything left?
                    if( strlen( $part ) > 0 ) {

                         // Trim these parts and put them on the row array
                         $row[] = trim( $part );
                    }
               }

               // Add this row to the table
               $table[ 'rows' ][] = $row;

          // List line
          } else if( strpos( $line, '* ' ) !== false ) {

               // Don't think this is ever going to happen, but better safe than sorry
               if( $inTable ) {

                    // We aren't in a table anymore
                    $inTable = false;
               
                    // Put the table onto the description array
                    $advantage[ 'description' ][] = [
                         'type' => 'table',
                         'table' => $table,
                    ];
               }

               // Are we already in a list?
               if( !$inList ) {

                    // We are now!
                    $inList = true;

                    // Set up a new list
                    $list = [];
               }

               // Clean up the line some
               $line = trim( substr( $line, 2 ) );

               // Find out where the first colon is
               $colon = strpos( $line, ':' );

               $list[] = [
                    'label' => trim( substr( $line, 0, $colon ) ),
                    'text' => trim( substr( $line, $colon + 1 ) ),
               ];

          // Just some basic text
          } else {

               // Were we working on a list?
               if( $inList ) {

                    // Well we're done
                    $inList = false;

                    // Put the list into the description array
                    $advantage[ 'description' ][] = [ 
                         'type' => 'list',
                         'list' => $list,
                    ];

                    // Reset the list just to be safe
                    $list = [];

               // Were we in a table?
               } else if( $inTable ) {

                    // Done with the table
                    $inTable = false;

                    // Put the table on the array
                    $advantage[ 'description' ][] = [
                         'type' => 'table',
                         'table' => $table,
                    ];

                    // Reset table array
                    $table = [];
               }

               // Remove whitespace
               $line = trim( $line );

               // Is there something left?
               if( strlen( $line ) > 0 ) {

                    // Put it on the description
                    $advantage[ 'description' ][] = $line;
               }
          }
     }

     // Just incase...
     if( count( $table ) > 0 ) {

          // Put the table into the description
          $advantage[ 'description' ][] = [
               'type' => 'table',
               'table' => $table,
          ];
     }

     // Checking list too
     if( count( $list ) > 0 ) {

          // Put this list on the description
          $advantage[ 'description' ][] = [ 
               'type' => 'list',
               'list' => $list,
          ];
     }

     // Push this advantage onto the array
     $parse[] = $advantage;
}


// Write our json file
file_put_contents( '../web/modules/custom/last_haiku_import/json/advantages-disadvantages.json', json_encode( $parse ) );

echo colorize([
     [ 
          'string' => 'Parsing file: ',
          'color' => '',
     ],
     [ 
          'string' => 'advantages.wiki,',
          'color' => 'bold_green',
     ],
     [
          'string' => " disadvantages.wiki",
          'color' => 'bold_red',
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
          'string' => " $advantages advantages ",
          'color' => 'green',
     ],
     [
          'string' => 'and ',
          'color' => '',
     ],
     [
          'string' => "$disadvantages disadvantages.\n",
          'color' => 'red',
     ],
     [    
          'string' => "     \u{029F} Saved to ",
          'color' => '',
     ],
     [
          'string' => "../web/modules/custom/last_haiku_import/json/advantages-disadvantages.json",
          'color' => 'dark_gray',
     ],
     [    
          'string' => ".\n\n",
          'color' => '',
     ],  
]);


function parseCost( $costString, $name ) {

     $points = [];

     // Do the special case ones first
     if( strpos( strtolower( $costString ), 'varies') !== false || strpos( strtolower( $costString ), 'variable') !== false ) {

          // Special Advantages
          if( $name == 'Allies' ) {
               $points = [ 1, 2, 4 ];

          } else if( $name == 'Blackmail' || $name == 'Blackmailed' ) {
               $points = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ];

          } else if( $name == 'Gentry' ) {
               $points = [ 8, 15, 18, 20, 25, 30 ];

          } else if( $name == 'Sacred Weapon' ) {
               $points = [ 3, 5, 6 ];

          // Special Disadvantages
          } else if( $name == 'Consumed' ) {
               $points = [ 4, 5, 6 ];

          } else if( $name == 'Dependent' ) {
               $points = [ 2, 3, 4, 5, 6 ];

          } else if( $name == 'Failure of Bushido' ) {
               $points = [ 3, 4, 6 ];

          } else if( $name == 'Imperial City Stigma' ) {
               $points = [ 4 ];

          // We shouldn't get here
          } else {

               // Output error text and exit
               echo "Error parseCost( $costString, $name )\n";
               exit();
          }

     // Costs which have an "or"
     } else if( strpos( strtolower( $costString), ' or ' ) !== false ) {

          if( $name == 'Languages' ) {
               $points = [ 1, 3 ];

          } else if( $name == 'Bounty' ) {
               $points = [ 2, 4, 6 ];

          // We shouldn't get here
          } else {

               // Output error text and exit
               echo "Error parseCost( $costString, $name )\n";
               exit();
          }
     
     // Some use a /. This inconsistentency is on AEG for a change
     } else if( strpos( $costString, '/' ) !== false ) {

          // Do the ones that are "points per rank"
          if( $name == 'Perceived Honor' ) {
               $points = [ 2, 4, 6, 8, 10, 12, 14, 16, 18, 20 ];

          } else if( $name == 'Wealthy' ) {
               $points = [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ];
          
          } else if( $name == 'Elemental Imbalance' ) {
               $points = [ 2, 4, 6, 8, 10, 12, 14, 16, 18, 20 ];

          // The rest should just be easy explodes
          } else {

               // Remove the extra stuff from the string
               $costString =  preg_replace("/[^0-9\/]/", "", $costString );

               // Simply blow it up on the /
               $points = explode( '/', $costString );

          }
     
     // Some use dashes!
     } else if( strpos( $costString, '-' ) !== false ) {

          // Remove the extra stuff from the string
          $costString =  preg_replace("/[^0-9\-]/", "", $costString );

          // Blow it up on the dash
          $range = explode( '-', $costString );

          // Get the values from the min to the max
          for( $i = $range[ 0 ]; $i <= $range[ 1 ]; $i++ ) {

               // Push the value onto the array
               $points[] = $i;
          }

     // Why not have a special one?
     } else if( strpos( $costString, 'Special' ) !== false ) {

          // Only one of these...
          if( $name == 'Inheritance: Water Hammer Armor' ) {
               $points = [ 4, 7, 12, 15 ];
     
          // We shouldn't get here
          } else {

               // Output error text and exit
               echo "Error parseCost( $costString, $name )\n";
               exit();
          }

     // Stupid Monks
     } else if( $name == 'Uncentered' ) {

          // Stupid monks!
          $points = [2, 4 ];

     // Stupid spider
     } else if( $name == 'Student of Shourido' ) {

          // Spider clan sucks.
          $points = [ 6, 9 ];

     // Spider clan still sucks
     } else if( $name == 'Stolen Identity' ) {

          // You guessed it, still sucks
          $points = [ 5, 6 ];

     // Another special case
     } else if( $name == 'Reincarnated' ) {

          $points = [ 5, 6 ];

     // Whats left should all be some various of "x points"
     } else if ( strpos( strtolower( $costString ), 'point' ) !== false ) {

          // Just strip out anything that isn't a number and throw it on there!
          $points = [ preg_replace("/[^0-9]/", "", $costString ) ];

     // We shouldn't get here
     } else {

          // Output error text and exit
          echo "Error parseCost( $costString, $name )\n";
          exit();
     }

     // Return the parsed point cost
     return $points;
}


function load_file( $file ) {
     // Try to load my dump of the families pages
     $handle = fopen("source/" . $file . ".wiki", "r");

     // Set up a placeholder for the lines of the file 
     $lines = [];

     // Ensure we loaded the file
     if ( $handle ) {
          
          // Read the next line in the file
          while ( ( $line = fgets( $handle )) !== false ) {
               $lines[] = $line;
          }
     }

	// Close the input file
	fclose( $handle );

     return $lines;
}
