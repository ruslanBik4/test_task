<?php
/*
  Есть продукты A, B, C, D, E, F, G, H, I, J, K, L, M. Каждый продукт стоит определенную сумму.
Есть набор правил расчета итоговой суммы:
Если одновременно выбраны А и B, то их суммарная стоимость уменьшается на 10% (для каждой пары А и B)
Если одновременно выбраны D и E, то их суммарная стоимость уменьшается на 5% (для каждой пары D и E)
Если одновременно выбраны E,F,G, то их суммарная стоимость уменьшается на 5% (для каждой тройки E,F,G)
Если одновременно выбраны А и один из [K,L,M], то стоимость выбранного продукта уменьшается на 5%
Если пользователь выбрал одновременно 3 продукта, он получает скидку 5% от суммы заказа
Если пользователь выбрал одновременно 4 продукта, он получает скидку 10% от суммы заказа
Если пользователь выбрал одновременно 5 продуктов, он получает скидку 20% от суммы заказа
Описанные скидки 5,6,7 не суммируются, применяется только одна из них
Продукты A и C не участвуют в скидках 5,6,7
Каждый товар может участвовать только в одной скидке. Скидки применяются последовательно в порядке описанном выше.
Необходимо написать программу на PHP, которая, имея на входе набор продуктов (один продукт может встречаться несколько раз) рассчитывала суммарную их стоимость. 
*/
class Kassa  {

	 // список продуктов 
	 static protected $products = Array(
		 'A' => 1000, 
		 'B' => 2000, 
		 'C' => 3000, 
		 'D' => 4000, 
		 'E' => 5000, 
		 'F' => 6000, 
		 'G' => 7000, 
		 'H' => 8000, 
		 'I' => 9000, 
		 'J' => 10000, 
		 'K' => 11000, 
		 'L' => 12000, 
		 'M' => 13000
	 );	 
	 // суммарные скидки
	 static protected $sales = Array(
		'A+B' => 0.9,
		'D+E' => 0.95,
		'E+F+G' => 0.95,
		'A[K,L,M]' => 0.95,
		 
	 );
	 // скидки на весь заказ от количества товаров
	 static protected $all_sales = Array(
		'3' => .95,
		'4' => .90,
		'5' => .80 
	 );
	 // товары, что не влияют на скидки от количества товаров
	 static protected $not_include = Array( 'A', 'C' );
	 // расчетная скидка на весь заказ
	 private $all_sale;
	 // итоговая сумма
	 protected $summa;
	 
	 
	 public function __construct( $input ) {
		
		// инициализация вычислимых значений
		$this->summa = 0; 
		$this->all_sale = 0;
		
		if ( is_array($input) )
		{
		    $this->GetAllSales($input);
		    $this->ComputeSalesRules($input);
		    foreach($input as $product => $count )
		    {
			  $this->summa += self::$products[$product]*$count ;
		    }
		}
	 }
	 // скидки на весь заказ от количества товаров
	 private function GetAllSales($input) {
		 // получаем список товаров
		 $products = array_keys($input);
		 // исключаем товары, которые не влияют на скидку
		 $products = array_diff( $products, self::$not_include );
		 
		 $count_products = count($products);
		 
		 // для простоты считаем, что у нас список сплошной, и проверяем только верхнюю границу
		 if ( array_key_exists($count_products, self::$all_sales) )
			 $this->all_sale = self::$all_sales[$count_products];
		 else if ( $count_products > 5)
			 $this->all_sale = self::$all_sales['5'];	         
	       
		 echo "Общая число товаров для скидки = $count_products, скидка = {$this->all_sale}<br>";
	 }
	 // проверяем суммарные скидки, сразу добавляем их в сумму заказа
	 private function ComputeSalesRules(&$input) {
			// проверяем условие суммарных скидок
			foreach(self::$sales as $sale => $price)
			{
				$products = split('\+', $sale);
				
				// в случае, если правило суммирования отсутствует
				if ( count($products) < 2 )
				{
					$this->ComputeImplementRule( $input, $sale, $price );
					continue;
					
				}
				
				$position = Array();
				
				// выискиваем совпадения в списке продуктов
				foreach($products as $product)
				{
					if ( array_key_exists( $product, $input) )
					   $position[$product] = $input[$product];
					else // в случае даже единственного несовпадения прекращаем подсчет 
					   break;
				}
				
				// не подсчитываем в случае несовпадения
				if( (count($position) < count($products)) 
					// либо нулевого количества товара (может произойти по ходу итераций)
					|| ( ($count_para = min($position)) < 1) )
					
					continue;
				
				$text = '';		
				// подсчитываем сумму совпавших пар с учетом скидки, сразу считаем сумму и убираем совпавшие товары из списка 
				foreach($position as $product => $count)
				{
					$input[$product] -= $count_para;
					
					$this->summa += self::$products[$product]*$count_para*$price * $this->all_sale;
					$text .= $product.' = '.self::$products[$product]*$price * $this->all_sale.' ';
					
				}
				echo "Правило '$sale' $count_para совпадений : $text<br>";
			}
		 
	 }
	 // вычисляем скидки для товаров правила №4
	 private function ComputeImplementRule( &$input, $sale, $price ) {
		 
		if ( !preg_match_all( '/(\w)(?=,|\])/', $sale, $macros, PREG_SET_ORDER  ) )
			return;
		
		
		$product = mb_substr( $sale, 0, 1 ); 
		// должен быть выбран первый товар для подсчета скидок
		if ( !array_key_exists( $product, $input) )
			return;
		
		$position = Array();
		
		// выискиваем совпадения в списке продуктов 
		foreach($macros as $products )
    		foreach($products as $product )
    		{
    			if ( array_key_exists( $product, $input) )
    			   $position[$product] = $input[$product];
    			 else
    			   echo $product;
    		}
		$text = '';		
		// подсчитываем сумму совпавших пар с учетом скидки 
		foreach($position as $product => $count)
		{
			
			$this->summa += self::$products[$product]*$count*$price * $this->all_sale;
			$text .= $product.' = '.self::$products[$product]*$price.' ';

			$input[$product] = 0; // в дальнейшем уже не учитіваем
			
		}
		echo "Правило '$sale'  совпадений : $text<br>";
	 }
	 private function AddSumma( $summa ) {
		 $this->summa += $summa;
	 }
	 
	 public function getSumma() {
		 
		 return 'Сумма с учетом всех скидок = '.$this->summa * $this->all_sale."<br>";
	 }
	 
	 // распечатка прайса
	 public function PrintPrice() {
		 Print_Array(self::$products);
	 }
 
 }
 
 /**
 * Пожалуйста, разработайте функцию\класс для "перемешивания" предложения.
Символ | является разделителем слов-вариантов. Например:
"{Пожалуйста|Просто} сделайте так, чтобы это {удивительное|крутое|простое} тестовое предложение {изменялось {быстро|мгновенно} случайным образом|менялось каждый раз}."
На выходе должно получаться:
"Пожалуйста сделайте так, чтобы это крутое тестовое предложение изменялось каждый раз." или "Просто сделайте так, чтобы это удивительное тестовое предложение изменялось мгновенно случайным образом".
 */
function Print_Array($rules) {
	foreach($rules as $key => $word)
		if ( is_array($word) || is_object($word) )
			Print_Array($word);
		else
			echo "$key = '$word' ";

	echo '<br>';
} 
function MixedWords($text) {
	
	$pattern = '/\{(?:(?:((?>[^|}{]*(?:(?R)))*[^|}{]*)\|)*([^|}{]+))\}/um';

	$pattern1 = '/\{(?:(?P<leaf>(?>[^{]+)*((?:([^|}{]+)\|)+)|(?P<root>(?R))*([^|}{]+))\}/u';

    preg_match_all( $pattern, $text, $macros, PREG_SET_ORDER  );
	Print_Array($macros);
	
	$search = Array();
	$replace= Array();
	
	foreach( $macros as $words )
	{
		$search[] = $words[0];
		$num_replace = mt_rand(1,4);
		while ( !array_key_exists($num_replace, $words) && ($num_replace >2) )
		  $num_replace--;
		  
		$replace[] = $words[$num_replace];
	}
	
	echo str_replace($search, $replace, $text);

}
 
 
 // список покупок
 $input = Array( 'A' => 4, 'E' => 1, 'F' => 4, 'G' => 1, 'B' => 2, 'M' => 2, 'K' =>1, 'L' => 3, 'M' => 2 );

 echo "Cписок покупок : <br>	";
 Print_Array($input);
 echo "<br>";
 
  $kassa = new Kassa( $input );
   
  echo $kassa->getSumma();
  
  echo 'Каталог товаров: <br>';
  $kassa->PrintPrice();

 $words = MixedWords('{Пожалуйста|Просто} сделайте так, чтобы это {удивительное|крутое|простое} тестовое предложение {изменялось {быстро|мгновенно} случайным образом|менялось каждый раз}.');

?>