<?
/*************************************************************/
/* This file contains all of the basic number calculation    */
/* functions.  They will only accept 1d arrays so make sure  */
/* you process your data before passing it to these functions*/
/*************************************************************/


/************************/
/* This is not yet done */
/************************/
function stem_and_leaf( $data, $stem ) {
  $tmp 		= string_number_sort( $data );
  $min 		= min( $tmp );
  $max 		= max( $tmp );
  $numElements	= num_elements( $tmp );

  $rmin = $min % $stem;
  $rmax = $max % $stem;
  
  $minStem = $min - $rmin;
  $maxStem = $max - $rmax;


  for( $i=0; $minStem + $stem * $i < $maxStem+1; $i++ ) {

    $stems[$i] = $minStem + $stem * $i;

    for( $j=0; $j < $numElements; $j++ ) {

      $foo = $i + 1;

      if( $tmp[$j] < $minStem + $stem * $foo && $tmp[$j] > $stems[$i]) {
        $leaves[$i] .= "$tmp[$j],";
      }
    }
    
  }
 
}


/***********************************/
/* This function will only process */
/* a one dimensional array         */
/***********************************/
function percentile( $data, $percentile ) {
  $tmp 		= string_number_sort( $data );
  $num_elements = num_elements( $tmp );

  if( 0 < $percentile && $percentile < 1 ) {
    $p = $percentile;
  }
  else if( 1 < $percentile && $percentile <= 100 ) {
    $p = $percentile * .01;
  }
  else {
    return -1;
  }

  if( $p < 1/($num_elements+1) || $p > $num_elements/($num_elements+1) ) {
    return -1;
  }

  $elementRef = $p*($num_elements+1);
  $r_1 = floor($elementRef);
  $r_2 = $r_1 + 1;
  $remainder = $elementRef - $r_1;
  
  $result = (1-$remainder)*$tmp[$r_1-1] + $remainder*$tmp[$r_2-1];
  return $result;
}


/***********************************/
/* This function will only process */
/* a one dimensional array         */
/***********************************/
function mid_range( $data ) {
  $diff = statRange( $data );
  $return = $diff / 2;

  return $return; 
}



/***********************************/
/* This function will only process */
/* a one dimensional array         */
/* Had to rename form range because*/
/* of a predefined php function	   */
/***********************************/
function stat_range( $data ) {
  $tmp = string_number_sort( $data );
  $min = min( $tmp );
  $max = max( $tmp );

  $diff = $max - $min;

  return $diff;
}



/***********************************/
/* This function will only process */
/* a one dimensional array         */
/***********************************/
function mode( $numIntervals, $data, $min, $max ) {
  $classIntervals = class_intervals( $numIntervals, $data, $min, $max );
  $maxFreq = max( $classIntervals[1] );

  for( $i=0; $i < count( $classIntervals[1])  ;$i++ ) {
    if( $classIntervals[1][$i] == $maxFreq ) { 
      $pos = $i; 
      break; 
    }
  }
  

  $parts = explode( ",", $classIntervals[0][$pos] );
  $min = $parts[0];
  $max = $parts[1];

  $classMark = (( $max - $min ) / 2) + $min;


  return $classMark;
}



/***********************************/
/* This function will only process */
/* a one dimensional array         */
/***********************************/
function median( $data ) {
  $median = $percentile( $data, 50 );
  return $median;
}



/***********************************/
/* This function will only process */
/* a one dimensional array         */
/***********************************/
function mean( $data ) {
  $num_elements 		= num_elements( $data );
  $sum 			= 0;
  $numValidNumElements 	= 0;

  for($i=0; $i < $num_elements; $i++ ) {
    if(  isset( $data[$i] )  ) {
      $sum += $data[$i];
      $numValidElements += 1;
    }
  }
  $mean = $sum / $numValidElements;

  return $mean;
}


/***********************************/
/* This function will only process */
/* a one dimensional array         */
/***********************************/
function variance( $data ) {
  $num_elements 		= num_elements( $data );
  $sum 			= 0;
  $numValidElements	= 0;

  $mean = mean( $data );


  for($i=0; $i < $num_elements; $i++ ) {
    if(  isset( $data[$i] )  ) {
      $tmp = $data[$i] - $mean;
      $sum += $tmp * $tmp;
      $numValidElements += 1; 
    }
  }

  $variance = $sum / ( $numValidElements - 1 );

  return $variance;
}



/***********************************/
/* This function will only process */
/* a one dimensional array         */
/***********************************/
function stddev( $data ) {
  return Sqrt( variance($data) );
}


function permutation( $n, $r ) {
  $numer = factorial( $n );
  $denom = factorial( $n -$r );

  $return = $numer / $denom;

  return $return;
}


function combination( $n, $r ) {
  $numer = factorial( $n );
  $denom = factorial($r) * factorial( $n -$r );

  $return = $numer / $denom;

  return $return;
}


function factorial( $x ) {
  $tmp = 1;

  if( $x == 0 ) {
    $tmp = 1;
  } 
  else if( $x < 0 ) {
    return -1;
  }
  else {
    for( $i=1; $i < $x+1; $i++ ) {
      $tmp *= $i;
    }
  }

  return $tmp;
}



/****************************************/
/* This function will take the data     */
/* find the frequency and relative      */ 
/* frequency for the given data and 	*/
/* store the results in a 2d array	*/
/****************************************/
function class_intervals( $numIntervals, $data, $min, $max ) {
  $dataSet = 1;
  $minSet = 1;
  $maxSet = 1;


  /* check that either we have data or min and max are set */
  if( !isset( $data ) || $data == "" ) { 
    $dataSet = 0;
  }
  else {
    $data = string_number_sort( $data );
  }
  if( !isset( $min ) || $min == "" ) {
    $minSet = 0;
  }
  if( !isset( $max ) || $max == "" ) {
    $maxSet = 0 ;
  }

  if( $dataSet == 0 && ( $minSet == 0 || $maxSet == 0 ) ){
    return -1;
  }   

  if( $minSet == 0 ) {
    $min = min( $data );
  }
  if( $maxSet == 0 ) {
    $max = max( $data );
  }


  /* Calculate our intervals */
  if( $min < $max ) {
    $range = $max - $min;
  }
  else {	
    return -1;	
  }


  if( !isset($numIntervals) || $numIntervals == "" ) {
    return -1;
  }


  $interval = $range / $numIntervals;
  $tmp = $min;

  for( $i=0; $i < $numIntervals; $i++ ) {
    $intervals[$i] = $tmp;
    $tmp += $interval;
    $intervals[$i] .= ",$tmp";
  }


  if( !isset( $data ) || $data == "" ) { 
    $results[0] = $intervals; 
    return $results;
  }


  $num_elements 		= num_elements( $data );
  $oneElementFrequency 	= 1/$num_elements;

  /***********************************************/
  /* Now we sort the data into correct intervals */
  /***********************************************/

  /* Zero out our array */
  for( $i=0; $i < $numIntervals; $i++ ){
    $frequency[$i] = 0;
    $relativeFrequency[$i] = 0.0;
  }


  for( $i=0; $i < $num_elements; $i++ ) {

    for($j=0; $j < $numIntervals; $j++) {
      
      $pos = strpos($intervals[$j], ",")+1;
      $len = strlen( $intervals[$j] );


      $tmp = substr( $intervals[$j], $pos, $len-$pos);


      if( $data[$i] <= $tmp ) { 
        $frequency[$j] += 1;
	$relativeFrequency[$j] += $oneElementFrequency;
        break;
      }
    }

  }
  
  $results[0] = $intervals; 
  $results[1] = $frequency; 
  $results[2] = $relativeFrequency; 

  return $results;
}



?>