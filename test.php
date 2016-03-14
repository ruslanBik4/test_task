<?php
  header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
  header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
  header ('Content-Type: text/html; charset=cp1251');

// ищем полиандром на участве симоволов от $i до $j
function FindPoliadrom( $text, $i, $j ) {

  $begin = $i; // запоминаем начало предполагаемого полиандрома
  $end   = $j; // ... конец ...
  
	while ( $i < $j )
	{
		// пробелы пропускаем
		while ( ($text[$i] == ' ') && ($j > $i) )
		   $i++;

		while ( ($text[$j] == ' ') && ($j > $i) )
		   $j--;
				
		// меняем края предполагаемого полиандрома при несовпадении символов
		while ( ( $text[$i] != $text[$j] ) && ($j > $i) )
		{
			if ( $begin != $i ) // откатываемся до начала строки и снова сравниваем
				$i = $begin;
			else                // если уже откатились - передвигаем конец подстроки
				$j--;
				
			$end   = $j;
		}
		   
		$i++;
		$j--;
	}

	if ( ($begin < $end) && !($i < $j) )
		return substr( $text, $begin, $end );
	else
	  return '';
}
/*
if ( !isset( $_REQUEST['text'] ) )
{
  echo 'нет параметра!';
 exit;
}
*/

 $text = 'аргентина манит неграаа';
 $len_test = strlen($text); 
//  $text = strtoupper( $text );
//  echo $text ;
 //$_REQUEST['text'];
 	
	$j = $len_test - 1; count()
	
	while ( $i < $j )
	{
		while ( ($text[$i] == ' ') && ($j > $i) )
		   $i++;

		while ( ($text[$j] == ' ') && ($j > $i) )
		   $j--;
		
		if ( $text[$i] != $text[$j] )
		   break;
		
		$i++;
		$j--;
	}

    // вся строка - палидром
    if ($i >= $j)
	    echo $text;
    else // проверяем все возможные полиандромы в строке
    {
	     $result = '';
	    // проходим последовательно по строке, исключая последний символ 
        for( $i=0; $i < $len_test-1; $i++ )
        {
	        $new_result = FindPoliadrom( $text, $i, $len_test - 1 );
	        // запоминаем наибольший
	        if ( strlen($result) < strlen($new_result) )
	              $result = $new_result;
        }
        
        if ($result)
	        echo $result;
        else
	        echo $_REQUEST['text'][0];
           

    }
    
?>