<?php

// Try to load my dump of the families pages
$handle = fopen("source/schools.wiki", "r");

// Set up a placeholder for our 
$array = [];

// Push our column headers onto the array
$array[] = [ 

];

// Ensure we loaded the file
if ( $handle ) {

	// Set up some variables for scope
	$clan = '';
	$basicBlock = [];
	$basicSchools = false;
	$advBlock = [];
	$advSchools = false;
	$altBlock = [];
	$altPaths = false;

	// Read the next line in the file
	while ( ( $line = fgets( $handle )) !== false ) {

		// Check if this is a line denoting a new clan
		if( strpos( $line, '+++' ) !== false ) {

			// Remove the leading markup
			$clan = substr( $line, 3 );

			// Remove any pesky whitespace
			$clan = trim( $clan );

			// Reset all content blocks
			$basicSchools = false;
			$advSchools = false;
			$altPaths = false;

		// Check if we have hit the basic schools block
		} else if( strpos( $line, '+ Basic Schools' ) !== false ) {
			
			// Set that we are in the basic skills sblock
			$basicSchools = true;

		// Check if we have hit the advanced schools block
		} else if( strpos( $line, '+ Advanced Schools' ) !== false ) {

			// Done with this clans basic skills
			$basicSchools = false;

			// Just incase something is out of order
			$altPaths = false;
			
			// Set that we are in the advanced schools path block
			$advSchools = true;
	
		// Check if we have hit the alternate paths block which is spelled differently some places
		} else if( strpos( $line, '+ Alternate Paths' ) !== false || strpos( $line, '+ Alternative Paths' ) !== false) {
			
			// We are done with advanced schools
			$advSchools = false;

			// Just incase some clan has no advanced schools
			$basicSchools = false;

			// Set that we are in the advanced schools path block
			$altPaths = true;
		
		// Not a new clan or type of school
		} else {

			// Are we working on basic schools?
			if( $basicSchools ) {

				// Push this line onto our basic block
				$basicBlock[ $clan ][] = $line;

			// Are we working on alternate paths?
			} else if( $altPaths ) {

				// Push this line onto the alternate paths block
				$altBlock[ $clan ][] = $line;

				// Are we working on advanced schools?
			} else if( $advSchools ) {

				// Push this line onto the advanced schools block
				$advBlock[ $clan ][] = $line;
			
			// If we hit this, something has gone wrong
			} else {
					echo error( $line );

					 exit();
			}
		}
	}

	// Close the file
    fclose( $handle );

	// The clans
	$clans = [
		'Crab',
		'Crane',
		'Dragon',
		'Lion',
		'Mantis',
		'Phoenix',
		'Scorpion',
		'Spider',
		'Unicorn',
		'Badger',
		'Bat',
		'Boar',
		'Dragonfly',
		'Falcon',
		'Fox',
		'Hare',
		'Monkey',
		'Oriole',
		'Ox',
		'Snake',
		'Sparrow',
		'Tiger',
		'Tortoise',
		'Imperial Families',
		'Ronin Families',
		'Firefly',
		'Bee',
		'Peacock',
		'Raven',
		'Salamander',
		'Shark',
		'Tanuki',
	
		// These last two aren't clans, but are seperate for whatever reason
		'Monk',
		'Miscellaneous'
	];

	// Set up arrays to hold the schools - splitting them into three files so its easier to work with
	$basicSchools = [];
	$advSchools = [];
	$altPaths = [];

	// Loop over each clans block
	foreach( $clans as $clan ) {
	
		// Make sure thi clan has basic schools
		if( array_key_exists( $clan, $basicBlock ) ) {

			// Parse out the basic schools for this clan
			$basicSchools[ $clan ] = basicSchoolsParse( $clan, $basicBlock[ $clan ] );
		}

		// Make sure this clan has advanced schools
		if( array_key_exists( $clan, $advBlock ) ) {

			// Parse out the advanced schools for this clan
			$advSchools[ $clan ] = advancedSchoolsParse( $clan, $advBlock[ $clan ] );
		}

		// Make sure this clan has altenrate paths
		if( array_key_exists( $clan, $altBlock ) ) {

			// Parse out the advanced schools for this clan
			$altPaths[ $clan ] = alternatePathsParse( $clan, $altBlock[ $clan ] );
		}
	}
	// Write out our JSON files
	file_put_contents( '../web/modules/custom/last_haiku_import/json/basic-schools.json', json_encode( $basicSchools ) );
	file_put_contents( '../web/modules/custom/last_haiku_import/json/advanced-schools.json', json_encode( $advSchools ) );
	file_put_contents( '../web/modules/custom/last_haiku_import/json/alternate-paths.json', json_encode( $altPaths ) );
}



function alternatePathsParse( $clan, $block ) {
	// Set up an array to hold our paths
	$paths = [];

	// Placeholder array to hold the path we're working on
	$path = [];

	// Placeholder flags
	$pathHeader = false;
	$pathTechnique = false;

	// Placeholder variables for techniques
	$techniqueDescription = [];
	$name = '';
	$rank = 0;

	// Loop through each line
	foreach( $block as $line ) {

		// Check if this is a new path
		if( strpos( $line, '++ ' ) !== false ) {

			// Check if there's anything in the path array
			if( count( $path ) > 0 ) {

				// Push the old school onto the Schools array
				$paths[] = $path;

				// Reset the school array
				$path = [];

				// Make sure we're done with the last path
				$pathTechnique = false;
			}


			// Indicate we have found a new path
			$pathHeader = true;

			// Find out where the school type starts
			$typeStart = strpos( $line, '[' );

			// Pull out the school name
			$pathName = substr( $line, 3, $typeStart - 4 );

			// Cut out the path name and leading [ of the path type
			$remainder = substr( $line, $typeStart + 1 );

			// Find out where the type ends
			$typeEnd = strpos( $remainder, ']' );

			// Get the path types by exploding the string on /
			$pathType = explode( '/', substr( $remainder, 0, $typeEnd ) );

			// Get whatever comes after the path type
			$remainder = trim( substr( $remainder, $typeEnd + 1 ) );

			// Assume we don't know the source of this path
			$pathSource = '';

			// See if there's a source
			if( strlen( $remainder ) > 0 ) {

				// Get the source
				$pathSource = substr( $remainder, 0, strlen( $remainder ) - 1 );

				// Remove any pesky space left in the string and the brackets
				$pathSource = trim( $pathSource, ' ][' );
			}

			// Put everything into the path array
			$path = [ 
				'name' => $pathName,
				'type' => $pathType,
				'source' => $pathSource,
				'description' => [],
				'rank' => [
					'basic' => [],
					'advanced' => [],
					'special' => '',
				],
			];	

		// CONSISTENCY?! WHAT IS THAT???
		} else if( strpos( $line, '* **Rank:**' ) !== false ) {

			// Probably don't need this here, but just in case indicate we are done with the header
			$pathHeader = false;
			
			// I still don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Rank' );

			// Is our line a number or something more?
			if( !is_numeric( $line ) ) {

				// One path replaces any rank
				if( $line == 'Any' ) {

					// Put it in the special category
					$path[ 'rank' ][ 'special'] = 'Any';

				// Is this an "or line
				} else if( strpos( $line, ' or ' ) !== false ) {

					// Blow it up on or and put it on the rank array
					$path[ 'rank' ][ 'basic' ] = explode( ' or ', $line );

				// If we hit this, something has gone wrong
				} else {
					echo error( $line );

					exit();
				}

			// It's just a number
			} else {

				// Safe to just push the line on as the rank
				$path[ 'rank' ][ 'basic' ][] = $line;				
			}

		// Monk schools have devotions
          } else if( strpos( $line, 'Devotion:' ) !== false ) {

               // Can just shove this on the array
               $path[ 'devotion' ] = parseLine( $line, 'Devotion' );

		// Rarely used, but used
		} else if( strpos( $line, '* **Technique Rank:**' ) !== false ) {

			// Probably don't need this here, but just in case indicate we are done with the header
			$pathHeader = false;

			// I still don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Technique Rank' );

			// This line is more complicated than just a number
			if( !is_numeric( $line ) ) {

				// Is this line the Kolat Master?
				if( strpos( strtolower( $line ), 'ranks above' ) !== false ) {

					$path[ 'rank' ][ 'special' ] = '6 (can also be taken at Insight Ranks above 6)';
				
				// Is this one of the "or" lines
				} else if ( strpos( strtolower( $line ), ' or ' ) !== false ) {
					
					// Explode the line on spaces
					$lineParts = explode( ' ', $line );

					// Loop through the parts
					foreach( $lineParts as $part ) {

						// Is this a number?
						if( is_numeric( $part ) ) {

							// Push that nuber onto the path array
							$path[ 'rank' ][ 'basic' ][] = $part;
						}
					}

				// Some use variable cause why not
				} else if( strtolower( $line ) == 'variable' ) {
					$path[ 'rank' ][ 'special' ] = 'Variable';
				
				// One path uses as + because why not
				} else if( strpos( $line, '+' ) !== false ) {
					$path[ 'rank' ][ 'special'] = '4+';
				} else if( strtolower( $line ) == 'special' ) {
					$path[ 'rank'][ 'special'] = 'Special';
				}

			// Just a normal, sensible line
			} else {

				// Push the rank onto the rank array
				$path[ 'rank' ][ 'basic' ][] = $line;
			}

		// We hit the replaces line
		} else if( strpos( $line, 'Replaces:' ) !== false ) {

			// We are done with the header
			$pathHeader = false;

			// Look at this consistency!
			$line = parseLine( $line, 'Replaces' );

			// Turn "or" into a comma
//			$line = str_replace( ' or ', ',', $line );

			// Put what it replaces onto the array
			$path[ 'replaces' ] = $line;

			// Is this the N/A bros?
			if( $line == 'N/A' ) {

				// How special
				$path[ 'rank' ][ 'special' ] = 'N/A';
			}

			// We only need to calculate the rank if wasn't already figured out
			if( strlen( $path[ 'rank' ][ 'special' ] ) == 0 && count( $path[ 'rank' ][ 'basic' ] ) == 0 ) {

				// Some paths use or instead of a comma
				$line = str_replace( ' or ', ',', $line );

				// Split the schools up
				$replacedSchools = explode( ',', $line );

				// Loop through each school
				foreach( $replacedSchools as $school ) {

					// Ensure we got a school, handles cases like "blah 4, or blech 3"
					if( strlen( $school ) > 0 ) {

						// Figure out the numeric value of this school
						$numbers = preg_replace('/[^0-9,]/', '', $school );

						// Ensure there's actually something here
						if( is_numeric( $numbers ) ) {

							// Check if this is an advanced school
							if( isAdvancedSchool( $school ) ) {

								// Push it onto the adv part of our rank array
								$path[ 'rank' ][ 'advanced' ][] = $numbers;

							// Must be basic school then
							} else {

								// Push it onto the basic list
								$path[ 'rank' ][ 'basic' ][] = $numbers;
							}
						}
					}
				}
			}

		// Some paths are basically school replacements
		} else if( strpos( $line, 'Benefit:' ) !== false ) {

			// Just in case
			$pathHeader = false;

			// I don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Benefit' );

			// Split off the amount and the actual trait
			$line = explode( ' ', $line );

			// The trait will always be "1", but just incase I'm going to extract it too
			$path[ 'benefitTrait' ] = $line[ 1 ];
			$path[ 'benefitAmount' ] = $line[ 0 ][ 1 ];

		// We hit the benefits line for skills
		} else if( strpos( $line, 'Skills:' ) !== false ) {

			// Just in case something is out of order
			$pathHeader = false;

			// I still don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Skills' );

			// Explode the skills into an array
			$skills = explode( ', ', $line );

			$path[ 'skills' ] = $skills;

		// We hit the benefits line for honor
		} else if( strpos( $line, 'Honor:' ) !== false ) {

			// Just in case something is out of order
			$schoolHeader = false;
			
			// I really don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Honor' );
			
			// Honor is easy
			$path[ 'honor' ] = $line;

		// We hit the benefits line for equipment
		} else if( strpos( $line, 'Outfit:' ) !== false ) {

			// Just in case something is out of order
			$schoolHeader = false;

			// It's not paranoia
			$line = parseLine( $line, 'Outfit' );
			
			// Outfit is easy
			$path[ 'outfit' ] = $line;
		
		// Shugenja schools have affinities and deficiencies
		} else if( strpos( $line, 'Affinity / Deficiency' ) !== false ) {

			// Clean up the line
			$line = parseLine( $line, 'Affinity / Deficiency' );

			// Whats left should be "<affinity> / <deficiency" so blow it up on the /
			$affDef = explode( '/', $line );

			// The first will always be affinity
			$path[ 'affinity' ] = trim( $affDef[ 0 ] );

			// The second will always be deficiency
			$path[ 'deficiency' ] = trim( $affDef[ 1 ] );

		// Requirements for the path
		} else if( strpos( $line, 'Requirements:' ) !== false || strpos( $line, 'Requires:' ) !== false ) {

			// I feel like at this point I should of made a function like "last_haikus_consistency_is_non_existant"
			// 	- Update: I did infact make a function because dear god
			$line = parseLine( $line, 'Requirements' );
			$line = parseLine( $line, 'Requires' );

			// This is often just some text, but can include traits, rings, and skills
			$path[ 'requirements' ] = $line;
			// Requirements for the path

		// It's the special line!
		} else if( strpos( $line, 'Special:' ) !== false ) {

			// Blah blah
			$line = parseLine( $line, 'Special' );

			// Nothing to do since it's just text
			$path[ 'special' ] = $line;

		// The technique for this alternate path
		} else if( strpos( $line, '* **Technique:**' ) !== false ) {

			// Indicate we've hit the technique - a few are multiline
			$pathTechnique = true;

			// Remove the technique portion
			$line = parseLine( $line, 'Technique' );

			// Find the position of the break in the technique
			$break = strpos( $line, ' - ' );

			$path[ 'technique' ][ 'name' ] = trim( substr( $line, 0, $break ), "/ " );

			// Pull out the first line of the descirption and wrap it in paragraph tags
			$path[ 'technique' ][ 'description' ][] = trim( substr( $line, $break ), "- " );

		// Just a line thats either empty or doesn't start with anything specific
		} else {

			// Is there actually anything on this line?
			if( strlen( trim( $line ) ) > 0 ) {

				// We just found the school header, so this must be the school description
				if( $pathHeader ) {

					// Put it on the array
					$path[ 'description' ][] = trim( $line );
				
				// Still in the technique 
				} else if( $pathTechnique ) {

					// Add this line to the description
					$path[ 'technique' ][ 'description' ][] = trim( $line );
				}
			}
		}
	}

	// As usual, the last path is forgotten. Poor dude.
	$paths[] = $path;

	// Return our paths
	return $paths;
}



function advancedSchoolsParse( $clan, $block ) {
	// Set up an array to hold our schools
	$schools = [];

	// Placeholder array to hold the school we're working on
	$school = [];

	// Placeholder flags
	$schoolHeader = false;
	$schoolTechniques = false;
	$techniqueBlock = false;

	// Placeholder variables for techniques
	$techniqueDescription = [];
	$name = '';
	$rank = 0;

	// Loop through the array
	foreach( $block as $line ) {

		// Check if this is a new school
		if( strpos( $line, '++ ' ) !== false ) {

			// Check if we have a technique waiting to be pushed onto the school array
			if( $rank > 0 ) {

				// Push the last technique onto the techniques array
				$school[ 'techniques' ][] = [
					'rank' => $rank,
					'name' => $name,
					'description' => $techniqueDescription,
				];

				// Reset the technique variables
				$rank = 0;
				$name = '';
				$techniqueDescription = [];
			}

			// Reset our flags since we're in a new school
			$schoolTechniques = false;

			// Check if there's anything in the school array
			if( count( $school ) > 0 ) {

				// Push the old school onto the Schools array
				$schools[] = $school;

				// Reset the school array
				$school = [];
			}

			// Indicate we have found a new school
			$schoolHeader = true;

			// Find out where the school type starts
			$typeStart = strpos( $line, '[' );

			// Pull out the school name
			$schoolName = substr( $line, 3, $typeStart - 4 );

			// Cut out the school name and leading [ of the school type
			$remainder = substr( $line, $typeStart + 1 );

			// Find out where the type ends
			$typeEnd = strpos( $remainder, ']' );

			// Get the school types by exploding the string on /
			$schoolType = explode( '/', substr( $remainder, 0, $typeEnd ) );

			// Get whatever comes after the school type
			$remainder = trim( substr( $remainder, $typeEnd + 1 ) );

			// Assume we don't know the source of this school
			$schoolSource = '';

			// See if there's a source
			if( strlen( $remainder ) > 0 ) {

				// Get the source
				$schoolSource = substr( $remainder, 0, strlen( $remainder ) - 1 );

				// Remove any pesky space left in the string and the brackets
				$schoolSource = trim( $schoolSource, ' ][' );
			}

			// Put everything into the school array
			$school = [ 
				'name' => $schoolName,
				'type' => $schoolType,
				'source' => $schoolSource,
				'description' => [],
			];
			
		// We hit the Requirements line
		} else if( strpos( $line, 'Requirements:' ) !== false ) {

			// Done with the header
			$schoolHeader = false;

		// We hit the rings/traits line
		} else if( strpos( $line, 'Status:' ) !== false ) {

			// Here I go again, not trusting last haiku
			$line = parseLine( $line, 'Status' );

			$school[ 'requirements'][ 'status' ] = $line;

		// We hit the glory line
		} else if( strpos( $line, 'Glory:' ) !== false ) {

			// Here I go again, not trusting last haiku
			$line = parseLine( $line, 'Glory' );

			$school[ 'requirements'][ 'glory' ] = $line;


		// We hit the Rings/Traits line
		} else if( strpos( $line, 'Rings/Traits:' ) !== false ) {

			// Here I go again, not trusting last haiku
			$line = parseLine( $line, 'Rings/Traits' );

			// Split the requirements
			$requirements = explode( ', ', $line );

			foreach( $requirements as $requirement ) {
				
				// Split off the name and rank
				$array = explode( ' ', $requirement );
			
				// Push this requirement onto the stats requirement block
				$school[ 'requirements' ][ 'stats' ][] = [
					'stat' => $array[ 0 ],
					'rank' => $array[ 1 ],
				];
			}

		// We hit the skills line
		} else if( strpos( $line, 'Skills:' ) !== false ) {

			// Just in case something is out of order
			$schoolHeader = false;

			// I still don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Skills' );

			// I still don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Skills' );

			// Parse out the skills into an overly complex array
			$school[ 'requirements' ][ 'skills' ] = parseSkills( $line );
/*
			// Explode the skills into an array
			$skills = explode( ', ', $line );

			foreach( $skills as $skill ) {

			}
*/
			// Put our skills array on the school array
//			$school[ 'requirements' ][ 'skills' ] = $skills;

		// We hit the requirements line honor
		} else if( strpos( $line, 'Honor:' ) !== false ) {

			// Just in case something is out of order
			$schoolHeader = false;
			
			// I really don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Honor' );
			
			// Honor is easy
			$school[ 'honor' ] = $line;

		// We hit the requirements line for advantages
		} else if( strpos( $line, 'Advantages:' ) !== false ) {

			// Just in case something is out of order
			$schoolHeader = false;

			// It's not paranoia
			$line = parseLine( $line, 'Advantages' );
			
			// Outfit is easy
			$school[ 'requirements' ][ 'advantages' ] = $line;

		// We hit the requirements line for other
		} else if( strpos( $line, 'Other:' ) !== false || strpos( $line, 'Restrictions:' ) !== false ) {

			// Just in case something is out of order
			$schoolHeader = false;

			// It's not paranoia
			$line = parseLine( $line, 'Other' );

			// Gotta do restrictions too
			$line = parseLine( $line, 'Restrictions' );
			
			// Outfit is easy
			$school[ 'requirements' ][ 'other' ] = $line;

		// We hit the techniques line
		} else if( strpos( $line, '__**Techniques**__' ) !== false ) {

			// Just being EXTRA SURE
			$schoolTechniques = false;

			// Indicate we are in the block of techniques
			$techniqueBlock = true;

		// Sometimes they do techniques like this...
		} else if( strpos( $line, '* **Technique Rank:**' ) !== false ) {

			// Trust in Last Haiku is at an all-time low
			$rank = trim( str_replace( '* **Technique Rank:**', '', $line ) );

		// We have hit a new technique
		} else if( strpos( $line, '**Rank' ) !== false ) {

			// Indicate we are in a technique
			$schoolTechniques = true;

			// Add the previous technique to the school array, if one exists
			if( $rank > 0 ) {

				// Push this technique onto the schools list of techniques
				$school[ 'techniques' ][] = [
					'rank' => $rank,
					'name' => $name,
					'description' => $techniqueDescription,
				];
			}

			// Get rid of the wiki markup
			$techniqueLine = str_replace( array( '**Rank ', '**' ), '', $line );
			
			// Split off the rank and the name
			$techniqueLine = explode( ':', $techniqueLine );

			// Get the rank
			$rank = trim( $techniqueLine[ 0 ] );

			// Remove that extra space in the name and cean up the string
			$name = trim( str_replace(  array( "//", "\n" ), '', $techniqueLine[ 1 ] ) );

			// Reset the description
			$techniqueDescription = [];

		// Just a line thats either empty or doesn't start with anything specific
		} else {

			// Is there actually anything on this line?
			if( strlen( trim( $line ) ) > 0 ) {

				// We just found the school header, so this must be the school description
				if( $schoolHeader ) {

					// Put it on the array
					$school[ 'description' ][] = trim( $line );
				
				// We're in techniques
				} else if( $schoolTechniques ) {
					
					// Remove pesky unneeded line breaks
					$line = str_replace(  array( "//", "\n" ), '', $line );

					// Wrap this description in a line
					$techniqueDescription[] = $line;
				}
			}
		}
	}

	// Push the last technique for the last school onto the techniques array
	$school[ 'techniques' ][] = [
		'rank' => $rank,
		'name' => $name,
		'description' => $techniqueDescription,
	];

	// The last school hasn't been pushed yet
	$schools[] = $school;

	// Return our finished advanced schools
	return $schools;
}


function basicSchoolsParse( $clan, $block ) {

	// Set up an array to hold our schools
	$schools = [];

	// Placeholder array to hold the school we're working on
	$school = [];

	// Placeholder flags
	$schoolHeader = false;
	$schoolTechniques = false;
	$techniqueBlock = false;

	// Placeholder variables for techniques
	$techniqueDescription = [];
	$name = '';
	$rank = 0;

	// Loop through the array
	foreach( $block as $line ) {

		// Check if this is a new school
		if( strpos( $line, '++ ' ) !== false ) {

			// Check if we have a technique waiting to be pushed onto the school array
			if( $rank > 0 ) {

				// Push the last technique onto the techniques array
				$school[ 'techniques' ][] = [
					'rank' => $rank,
					'name' => $name,
					'description' => $techniqueDescription,
				];

				// Reset the technique variables
				$rank = 0;
				$name = '';
				$techniqueDescription = [];
			}

			// Reset our flags since we're in a new school
			$schoolTechniques = false;

			// Check if there's anything in the school array
			if( count( $school ) > 0 ) {

				// Push the old school onto the Schools array
				$schools[] = $school;

				// Reset the school array
				$school = [];
			}

			// Indicate we have found a new school
			$schoolHeader = true;

			// Find out where the school type starts
			$typeStart = strpos( $line, '[' );

			// Pull out the school name
			$schoolName = substr( $line, 3, $typeStart - 4 );

			// Cut out the school name and leading [ of the school type
			$remainder = substr( $line, $typeStart + 1 );

			// Find out where the type ends
			$typeEnd = strpos( $remainder, ']' );

			// Get the school types by exploding the string on /
			$schoolType = explode( '/', substr( $remainder, 0, $typeEnd ) );

			// Get whatever comes after the school type
			$remainder = trim( substr( $remainder, $typeEnd + 1 ) );

			// Assume we don't know the source of this school
			$schoolSource = '';

			// See if there's a source
			if( strlen( $remainder ) > 0 ) {

				// Get the source
				$schoolSource = substr( $remainder, 0, strlen( $remainder ) - 1 );

				// Remove any pesky space left in the string and the brackets
				$schoolSource = trim( $schoolSource, ' ][' );
			}

			// Put everything into the school array
			$school = [ 
				'name' => $schoolName,
				'type' => $schoolType,
				'source' => $schoolSource,
				'description' => [],
			];

		// We hit the benefits line
		} else if( strpos( $line, 'Benefit:' ) !== false ) {

			// Done with the header
			$schoolHeader = false;

			// I don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Benefit' );

			// Split off the amount and the actual trait
			$line = explode( ' ', $line );

			// The trait will always be "1", but just incase I'm going to extract it too
			$school[ 'benefitTrait' ] = $line[ 1 ];
			$school[ 'benefitAmount' ] = $line[ 0 ][ 1 ];

		// We hit the benefits line
		} else if( strpos( $line, 'Skills:' ) !== false ) {

			// Just in case something is out of order
			$schoolHeader = false;

			// I still don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Skills' );

			// Parse out the skills into an overly complex array
			$school[ 'skills' ] = parseSkills( $line );


		// We hit the benefits line honor
		} else if( strpos( $line, 'Honor:' ) !== false ) {

			// Just in case something is out of order
			$schoolHeader = false;
			
			// I really don't trust last Haikus formatting to be consistent
			$line = parseLine( $line, 'Honor' );
			
			// Honor is easy
			$school[ 'honor' ] = $line;

		// Shugenja schools have affinities and deficiencies
		} else if( strpos( $line, 'Affinity / Deficiency' ) !== false ) {

			// Clean up the line
			$line = parseLine( $line, 'Affinity / Deficiency' );

			// Whats left should be "<affinity> / <deficiency" so blow it up on the /
			$affDef = explode( '/', $line );

			// Check if this is the school with no affinity or deficiency
			if( strpos( $line, 'None/' ) !== false ) {

				// Make it a special affinity
				$school[ 'affinitySpecial' ] = 'None / None';

				// Make affDef empty
				$affDef = [];
			
			// Stupid free ogres
			} else if( strpos( $line, 'N/A' ) !== false ) {

				// They are N/A which i suppose is different than None / None
				$school[ 'affinitySpecial' ] = 'N/A';

				// Make it empty again
				$affDef = [];
			}

			// Check if we got back two parts
			if( count( $affDef ) > 1 ) {

				// The first will always be affinity
				$school[ 'affinity' ] = trim( $affDef[ 0 ] );

				// The second will always be deficiency
				$school[ 'deficiency' ] = trim( $affDef[ 1 ] );
			
			// A few schools have special text for their affinity/deficiency
			} else {
				$school[ 'affinitySpecial' ] = trim( $line );
			}

		// Monk schools have devotions
		} else if( strpos( $line, 'Devotion:' ) !== false ) {

			// Can just shove this on the array
			$school[ 'devotion' ] = parseLine( $line, 'Devotion' );

		// Shugenja also have spells
		} else if( strpos( $line, 'Spells:' ) !== false ) {

			// Format our line
			$line = parseLine( $line, 'Spells' );

			// Put the spells on the array
			$school[ 'spells' ] = $line;

		// We hit the benefits line for equipment
		} else if( strpos( $line, 'Outfit:' ) !== false ) {

			// Just in case something is out of order
			$schoolHeader = false;

			// It's not paranoia
			$line = parseLine( $line, 'Outfit' );
			
			// Outfit is easy
			$school[ 'outfit' ] = $line;

		// Shugenja have special technqiues
		} else if( strpos( $line, '* **Technique:**' ) !== false ) {
			// Remove the technique portion
				$line = parseLine( $line, 'Technique' );

				// Find the position of the break in the technique
				$break = strpos( $line, ' - ' );
	
				// Set up an array to hold the parts of the technqiue, rank will be 0
				$schoolTechnique = [
					'rank' => 0,
				];

				// Put out the name
				$schoolTechnique[ 'name' ] = trim( str_replace(  array( "//", "\n" ), '', substr( $line, 0, $break ) ), "/ " );
	
				// Pull out the first line of the descirption
				$schoolTechnique[ 'description' ][] = trim( substr( $line, $break ), "- " );

				// Put the technqiue on the technqiues block
				$school[ 'techniques' ][] = $schoolTechnique;

		// We hit the techniques line
		} else if( strpos( $line, '__**Techniques**__' ) !== false ) {

			// Just being EXTRA SURE
			$schoolTechniques = false;

			// Indicate we are in the block of techniques
			$techniqueBlock = true;

		// We have hit a new technique
		} else if( strpos( $line, '**Rank' ) !== false ) {

			// Indicate we are in a technique
			$schoolTechniques = true;

			// Add the previous technique to the school array, if one exists
			if( $rank > 0 ) {

				// Push this technique onto the schools list of techniques
				$school[ 'techniques' ][] = [
					'rank' => $rank,
					'name' => $name,
					'description' => $techniqueDescription,
				];
			}

			// Get rid of the wiki markup
			$techniqueLine = str_replace( array( '**Rank ', '**' ), '', $line );
			
			// Split off the rank and the name
			$techniqueLine = explode( ':', $techniqueLine );

			// Get the rank
			$rank = trim( $techniqueLine[ 0 ] );

			// Remove that extra space in the name
			$name = trim( $techniqueLine[ 1 ] );

			// Reset the description
			$techniqueDescription = [];

		// Just a line thats either empty or doesn't start with anything specific
		} else {

			// Is there actually anything on this line?
			if( strlen( trim( $line ) ) > 0 ) {

				// We just found the school header, so this must be the school description
				if( $schoolHeader ) {

					// Put it on the array
					$school[ 'description' ][] = trim( $line );
				
				// We're in techniques
				} else if( $schoolTechniques ) {
					
					// Remove pesky unneeded line breaks
					$line = str_replace( "\n", '', $line );

					// Wrap this description in a line
					$techniqueDescription[] = trim( $line );
				}
			}
		}
	}

	// Ensure there is a technqiue to push
	if( strlen( $name ) > 0 ) {
		// Push the last technique for the last school onto the techniques array
		$school[ 'techniques' ][] = [
			'rank' => $rank,
			'name' => $name,
			'description' => $techniqueDescription,
		];
	}

	// The last school hasn't been pushed yet
	$schools[] = $school;

	// Return our finished basic schools
	return $schools;
}

// The inconsistency finally got to me, time to simplfy this
function parseLine( $line, $word ) {

	// Look at this consistency!
	return trim( str_replace( [ "* **$word:**", "* **$word:", "* $word:", "**$word:**" ], '', $line ) );

}

function error( $line ) {
	return "!!! ERROR !!! !!! ERROR !!! !!! ERROR !!! !!! ERROR !!!\n" .
	"!!! ERROR !!! !!! ERROR !!! !!! ERROR !!! !!! ERROR !!!\n" .
	"!!! ERROR !!! !!! ERROR !!! !!! ERROR !!! !!! ERROR !!!\n" .
	"------------------------------------\n" .
	"$line\n" .
	"------------------------------------\n";	
}


function parseSkills( $remainder ) {
	
	// Place to put our skills
	$skills = [];

	// Place to put something that is an "any" line
	$any = [];

	// Flags so we know what we're working on
	$pickAny = false;
	$emphasisBlock = false;

	// Placeholders
	$emphasisBlockSkill = '';
	$emphasisBlockEmphases = [];

	// Find out how many commas there are
	$count = substr_count( $remainder, ',' );

	// Loop while we have text
	while( strlen( $remainder ) > 0 ) {

		// Parse out whats before the next comma
		$portion = substr( $remainder, 0, strpos( $remainder, ',' ) );

		// If there isn't a comma, we are in the last part of the string
		if( strpos( $remainder, ',' ) === false ) {
			$portion = $remainder;
		}

		// Find out if there is an open paran
		$openPara = strpos( $portion, '(' );

		// Find out if there is a closing paran
		$closePara = strpos( $portion, ')' );

		// A list of "pick some of these"
		if( ( strpos( $portion, 'At least one' ) !== false || strpos( $portion, ' any ' ) !== false ) && !$emphasisBlock ) {

			// Find out if there is a colon
			$colon = strpos( $portion, ':' );
	
			// If theres a colon, then its a pick from a list of specific skills
			if( $colon !== false ) {

				// We're in a pick any
				$pickAny = true;

				// Parse out the any portion
				$any[ 'text' ] = trim( substr( $portion, 0, $colon ) );
				
				// The thing right after the colon will be a skill
				$any[ 'skills' ][] = trim( substr( $portion, $colon + 1 ) );

			// No colon, it's just a pick from a category
			} else {

				$skills[ 'any' ] = trim( $portion );
			}

		// This is a skill with only one emphasis
		} else if( $openPara !== false && $closePara !== false ) {

			// Pull out the skill and emphasis
			$skill = trim( substr( $portion, 0, $openPara ) );
			$emphasis = trim( substr( $portion, $openPara + 1 ) );

			// Put it on the skills array
			$skills[ $skill ] = trim( $emphasis, ')' );

		// This is a skill with at least two emphases
		} else if( $openPara !== false ) {

			// Indicate we're in an emphasis block
			$emphasisBlock = true;

			// Empty array to hold our emphases
			$emphasisBlockEmphases = [];

			// Figure out the skill we're going  to be using
			$emphasisBlockSkill = trim( substr( $portion, 0, $openPara ) );

			// The first emphasis will be in this block of text as well
			$emphasisBlockEmphases[] = trim( substr( $portion, $openPara + 1 ) );
		
		// We hit the end of our emphasis list
		} else if( $closePara !== false ) {

			// Indicate we're done with this block
			$emphasisBlock = false;

			// Get the final emphasis
			$emphasis = trim( str_replace( ' or ', '', substr( $portion, 0, $closePara ) ) );

			// Push this last emphasis onto the array
			$emphasisBlockEmphases[] = $emphasis;

			// Are we on a pick any? If so push this into the any array
			if( $pickAny ) {
				$any[ 'skills' ][ $emphasisBlockSkill ] = $emphasisBlockEmphases; 
			} 
		
		// Are we in an emphasis block?
		} else if( $emphasisBlock ) {

			// Push this skill onto the emphasis array
			$emphasisBlockEmphases[] = trim( $portion );

		// Nothing special about this portion
		} else {

			// If we are in a pick any, add it to the pick any
			if( $pickAny ) {

				// Push it on there
				$any[] = trim( $portion );

			// Just a normal skill
			} else {

				// So add it to the skills list
				$skills[] = trim( $portion );
			}
		}

		// Remove the thing we just parsed from the line
		$remainder = substr( $remainder, strlen( $portion ) + 1 );
	}

	// Is there anything in the any arrays
	if( count( $any ) ) {

		// Push the any block onto the skills array
		$skills[ 'pickAny' ] = $any;
	}

	// Return our skills
	return $skills;
}

function isAdvancedSchool( $school ) {
	
	$advancedSchools = [
		"Akodo Tactical Master",
		"Asako Inquisitors",
		"Children of Doji",
		"Daidoji Harriers",
		"Dark Paragon",
		"Defender of the Wall",
		"Disciples of Sun Tao",
		"Elemental Guard",
		"Imperial Scion",
		"Kakita Master Artisan",
		"Kenshinzen",
		"Kitsu Sodan-Senzo",
		"Kobune Captain",
		"Kolat Assassin",
		"Legion of Two Thousand",
		"Maho-Bujin",
		"Mirumoto Master Sensei",
		"Obsidian Warrior",
		"Peacock Provocateur",
		"Scorpion Instigator",
		"Scorpion Saboteur",
		"Storm Riders",
		"Swordmasters",
		"Tamori Master of the Mountain",
		"The Lion's Pride",
		"The White Guard",
		"Tsudao’s Legion",
	];

	foreach( $advancedSchools as $advancedSchool ) {
		if( strpos( $advancedSchool, $school ) !== false ) {
//		if( in_array( $school, $advancedSchools ) ) {
			return true;
		}
	}

	return false;
}

?>