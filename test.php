<?php
  header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
  header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
  header ('Content-Type: text/html; charset=cp1251');

// ���� ���������� �� ������� ��������� �� $i �� $j
function FindPoliadrom( $text, $i, $j ) {

  $begin = $i; // ���������� ������ ��������������� �����������
  $end   = $j; // ... ����� ...
  
	while ( $i < $j )
	{
		// ������� ����������
		while ( ($text[$i] == ' ') && ($j > $i) )
		   $i++;

		while ( ($text[$j] == ' ') && ($j > $i) )
		   $j--;
				
		// ������ ���� ��������������� ����������� ��� ������������ ��������
		while ( ( $text[$i] != $text[$j] ) && ($j > $i) )
		{
			if ( $begin != $i ) // ������������ �� ������ ������ � ����� ����������
				$i = $begin;
			else                // ���� ��� ���������� - ����������� ����� ���������
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
  echo '��� ���������!';
 exit;
}
*/

 $text = '��������� ����� �������';
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

    // ��� ������ - ��������
    if ($i >= $j)
	    echo $text;
    else // ��������� ��� ��������� ����������� � ������
    {
	     $result = '';
	    // �������� ��������������� �� ������, �������� ��������� ������ 
        for( $i=0; $i < $len_test-1; $i++ )
        {
	        $new_result = FindPoliadrom( $text, $i, $len_test - 1 );
	        // ���������� ����������
	        if ( strlen($result) < strlen($new_result) )
	              $result = $new_result;
        }
        
        if ($result)
	        echo $result;
        else
	        echo $_REQUEST['text'][0];
           

    }
    
?>