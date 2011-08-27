<?php
/***********************************/
/* This function will only process */
/* a one dimensional array         */
/***********************************/
function mean( $data ) {
  $num_elements 		= count( $data );
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
//NB. CURRENTLY CONFIGURED FOR SAMPLE VARIANCE
//TO CHANGE TO POPULATION, CHANGE THE $numValidElements - 1
//AT THE FUNCTION BOTTOM TO $numValidElements - 0.
function variance( $data ) {
  $num_elements 		= count( $data );
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
  return sqrt( variance($data) );
}
?>
