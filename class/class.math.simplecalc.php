<?php
/**
* Class to execute simple mathematical calculations with expression in infix notation
* Curently allows simple algebraic binary operations:
* - + (addition)
* - - (substraction)
* - * (multiplication)
* - / (division)
* - ^ (exponential)
* It also allows to use braces and watchs for priority of operations
* @author Vladimir Shugaev <vladimir.shugaev@junvo.com>
* @copyright Vladimir Shugaev <vladimir.shugaev@junvo.com>
* @license GNU GPL v2 or GNU LGPL
**/

/*
USAGE
====
$testExpressions=array(
	'3+4'=>3+4, //test addition
	'5-2'=>5-2, //test substraction
	'-4-(-3)' =>-4-(-3), //test unary substraction
	'3*4'=>3*4, //test multiplication
	'18/5'=>18/5, //test division
	'2^3'=>pow(2,3), //test exponential
	'3+2*5'=>3+2*5, //test operations priority
	'(3+2)*5'=>(3+2)*5, //test braces
	'( 3+ 2) *5 ' => (3+2)*5, //test removing of whitespaces
	'2+3-(3*2)^2' => 2+3-pow((3*2),2),
	'3+2-1/3^2-15*1' =>3+2-pow(1/3,2)-15*1,
	'-1/3^2-15*1'=>-1*pow(1/3,2)-15*1,
	'1-15*1'=>1-15,
	'3+2-1/3^2-15*1+(2+3-(3*2)^2)'=>3+2-pow(1/3,2)-15*1+(2+3-pow((3*2),2))
);
include "class.SimpleCalc.php";
$calc=new SimpleCalc();
$consts=array(
	'G'=>6.6742*pow(10,-11)
);
$vars=array(
	'R'=>6.371*pow (10,6), //meters (mass of Earth)
	'M'=>5.9736*pow (10,24) //kilograms (radius of Earth)
);
$formula='G*M/R^2'; //gravity force of object with mass M kg on distance R from its center

$toCalc=str_replace(array_keys($consts),array_values($consts),$formula);
$toCalc=str_replace(array_keys($vars),array_values($vars),$toCalc);
$testExpressions[$toCalc]=6.6742E-11*5.9736E+24/pow(6371000,2);
?>
<?php
foreach ($testExpressions as $exp => $res){
	$calculated=$calc->calculate ($exp);
	echo '<tr class="'.($res==$calculated?'true':'false').'">';
	echo '<td>'.$exp.'</td>';
	echo '<td>'.$calculated.'</td>';
	echo '<td>'.$res.'</td>';
	echo '</tr>';
}
?>
*/
//ORG NAME -> class.SimpleCalc, ORG CLASS NAME -> SimpleCalc
class simplecalc{

	/**
	* Priority of operations
	**/
	var $priority=array(
		'^'=>3,
		'*'=>2,
		'/'=>2,
		'+'=>1,
		'-'=>1
	);

	/**
	* Prepares expression for calculations and calculates it with SimpleCalc::exec. Returns the result of calculations
	* @param string $expression - mathematical expression in infix notation
	* @return float - result of calculation of $expression
	**/
	public function calculate($expression){
		$expression=str_replace(' ','',$expression);
		return $this->exec($expression);
	}

	/**
	* Recursively parses $expression and returns the result
	* @param string $expression - mathematical expression in infix notation without spaces
	* @return float - result of calculation of $expression
	**/
	private function exec($expression){
		if (is_numeric($expression))
			return ($expression);
		else{
			$parsed=$this->parseExpression($expression);
			$parsed[0]=empty($parsed[0])?0:$parsed[0];
			switch ($parsed['operator']){
				case '+':
					$r = $this->exec($parsed[0])+$this->exec($parsed[1]); break;
				case '-':
					$r = $this->exec($parsed[0])-$this->exec($parsed[1]); break;
				case '*':
					$r = $this->exec($parsed[0])*$this->exec($parsed[1]); break;
				case '/':
					$r = $this->exec($parsed[0])/$this->exec($parsed[1]); break;
				case '^':
					$r = pow($this->exec($parsed[0]),$this->exec($parsed[1])); break;
				case '(':
					$r = $this->exec($parsed[0]); break;
			}
			return $r;
		}
	}

	/**
	* Explodes $expression by the operation with the lowest priority
	* @param string $expression mathematical expression in infix notation without spaces
	* @return array Array of operands with numerical indexes and operator with 'operator' key
	**/
	private function parseExpression($expression){
		//state 0 for seeking operatorors
		//state 1 for skeeping braces
		$state=0;

		//ammount of opened and not closed braces to current position
		$braces=0;

		//operators position and priority
		$operatorsPositions=array();
		$operators=array_keys($this->priority);

		$len=strlen($expression);
		for ($position=0; $position<$len; $position++){
		$char = $expression{$position};
			switch ($state){
				case 0:
					if (array_search($char, $operators)!==false){
						if (($char=='+'||$char=='-')&&($expression{$position-1}=='E')) //ignore + and - in exponential like 6.6742E-11
							continue;
						$operatorsPositions[$position]=$this->priority[$char];
					}
					if ($char =='('){
						$braces++;
						$state=1;
					}
					break;
				case 1:
					if ($char=='(')
						$braces++;
					if ($char==')')
						$braces--;
					if ($braces==0)
						$state=0;
					break;
			}
		}

		if (count($operatorsPositions)){
			$minPriority=min (array_values($operatorsPositions));
			$dividePositions=array_keys($operatorsPositions, $minPriority);
			$dividePosition=max ($dividePositions);
			$operator=$expression[$dividePosition];

			$result=array(
				'operator'=>$operator,
				substr($expression,0,$dividePosition),
				substr($expression,$dividePosition+1)
			);
			return $result;
		}

		else{
			$result=array(
				'operator'=>'(',
				substr($expression,1,-1)
			);
			return $result;
		}
	}
}
?>