<?php
function radix_sort( &$arr) {

    // Find the number of passes needed to complete the sort
    $passes = strlen(  max($arr) );
    $buckets = [];

    // Start the passes
    for($i = 1; $i <= $passes; $i++) {
        // Create - reinitialize some buckets
        for ($b = 0; $b <= 9; $b++) {
            $buckets[$b] = [];
        }
        
        for ($j = 0; $j < count($arr); $j++) {
		    // Drop into the proper bucket based on the significant digit
		    $numStr = $arr[$j];
		    $numLen = strlen(  $numStr) ;
		    if ($numLen < $i) {
		        $bucketsIndex = 0;
		    } else {
		        $bucketsIndex = $numStr[$numLen - $i];
		    }
		    array_push($buckets[$bucketsIndex], $arr[$j]);
		}


        // Repopulate our array by pulling out of our buckets
        $k = 0;
        foreach ($buckets as $bucket) {
            foreach ($bucket as $value) {
                $arr[$k] = $value;
                $k++;
            }
        }
    }
 return array_map ('intval', $arr);
}	


   error_reporting(E_ALL);
	echo "Start test in ".strftime('%H : %M : %S', time() )."<br>";

	echo 'Start memory usage: '.(memory_get_usage(TRUE) / 1024)."<br>";

  define("LIMIT", 1e6*3);
try
{	 

	$big_arr =  /* range( 1, LIMIT ); // */ new SplFixedArray( LIMIT );
	
	$s = microtime(true);
	 
	foreach( $big_arr as /* $i => */ &$value )
		  $value = 1 * mt_rand(1, LIMIT);
	 
	echo "End of filling array ($i elements) from ".(microtime(true) - $s)." s <br>"; 
	
	$s = microtime(true);

	$big_arr = radix_sort( array_map ('strval', $big_arr ) ); 

	echo "End of sorting array ({count($big_arr)} elements) from ".(microtime(true) - $s)." s <br>";

	foreach( $big_arr as $i => $value )
	 if ( $value > $big_arr[$i+1] )
	 {
		echo /* array_search( $i, $big_arr). */$value." > ".$big_arr[$i+1]."<br>";  
		break;
	 }

     echo $big_arr[1].' '.$big_arr[2].' '.$big_arr[3]."<br>";
	$s = microtime(true);

}
catch( Exception $e)
{
	echo $e->getMessage();
}	  
	echo 'End memory usage: '.(memory_get_usage(TRUE) / 1024)."<br>";
	echo 'Peak memory usage: '.(memory_get_peak_usage(TRUE) / 1024)."<br>";
	echo "End test in ".strftime('%H : %M : %S', time() )."<br>";
?>