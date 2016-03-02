<?php
	namespace Rest\Library\Common;
	
	use Zend\Crypt\PublicKey\Rsa\PublicKey;
	use libs\Library\MobileDetect\MobileDetect;

	class Common{
		const SALTCAS		= 'casWebSalt';
		
		const MOBILE 		= 'MOBILE';
		
		const TABLET 		= 'TABLET';
		
		const IPHONE 		= 'IPHONE';
		
		const ANDROID		= 'ANDROID';
		
		const PC 			= 'PC';
		
		const WINDOWS 		= 'WINDOWS';
		
		const LINUX 		= 'LINUX';
		
		const MAC 			= 'MACINTOSH';
		
		const IPAD 			= 'IPAD';
		
		const SALT 			= 'cas';
		
		static $_today;
		
		/**
		 * Retorna a data de hoje no formato inglês ou brasileiro.
		 * 
		 * @param string $pFormat
		 * 
		 * @return string
		 */
		public static function getToday($pFormat = 'EN'){
			if($pFormat == 'EN')
				return date('Y-m-d');
			else 
				return date('d/m/Y');
		}
		
		/**
		 * 
		 * @return string
		 */
		public static function getDateZero(){
			return '0000-00-00 00:00:00';
		}
		
		/**
		 * 
		 * @param unknown_type $str
		 */
		public static function encript($str){
			self::$_today = date('YmdHis');
			$string = self::SALT . ";" . $str . ";" . self::$_today;
			$enc = base64_encode($string);
			
			return $enc;
		}
		
		/**
		 * 
		 * @param unknown_type $enc
		 * @return string
		 */
		public static function decript($enc){
			$string = base64_decode($enc);
			return $string;
		} 
		
		/**
		 * 
		 * @param unknown_type $pFormat
		 */
		public static function getDateNow($pFormat = 'EN'){
			if($pFormat == 'EN')
				$date = date('Y-m-d H:i:s');
			else 
				$date = date('d/m/Y H:i:s');
				
			return $date;
		}
		
		/**
		 * 
		 * @param unknown_type $pFormat
		 * @return string
		 */
		public static function getEndPlan($pFormat = 'EN'){
			if($pFormat == 'EN'){
				$date = strftime('%Y-%m-%d 23:59:59', strtotime('+ '. 12 .' months'));
			}
			else{
				$date = strftime('%d/%m/%Y 23:59:59', strtotime('+ '. 12 .' months'));
			}
			
			return $date;
		}
		
		/**
		 * 
		 * @param unknown_type $pQtdMonths
		 * @param unknown_type $pFormat
		 */
		public static function getNextPay($pQtdMonths = 1, $pFormat = 'EN'){
			if($pFormat == 'EN'){
				$date = strftime('%Y-%m-%d', strtotime('+ '. $pQtdMonths .' months'));
			}
			else{
				$date = strftime('%d/%m/%Y', strtotime('+ '. $pQtdMonths .' months'));
			}
			
			return $date;
		}
		
		/**
		 * 
		 * @param unknown_type $pAllow
		 */
		public static function allowUrlFopen($pAllow = true){
			ini_set("allow_url_fopen", $pAllow);
		}
		
		/**
		 * 
		 * @param unknown_type $pString
		 * @return mixed
		 */
		public static function removeAccent($pString){
			return preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $pString ) );
		}
		
		/**
		 * 
		 * @param unknown_type $pSlug
		 * @return string
		 */
		public static function removeShortWordsFromSlug($pSlug){
			$slug = explode('-', $pSlug);
			foreach($slug as $key => $value){
				if(strlen($value) < 3)
					unset($slug[$key]);
			}
			
			return implode('-', $slug);
		}
		
		/**
		 * 
		 */
		public static function baseRedirect(){
			return @$_SERVER['REDIRECT_BASE'];
		}
		
		/**
		 * 
		 * @return string
		 */
		public static function baseUrl(){
			return 'http://' . $_SERVER['HTTP_HOST'];
		}
		
		/**
		 * 
		 * @return string
		 */
		public static function baseUrlToApi(){
			$requestScheme 	= $_SERVER['REQUEST_SCHEME'];
			$host 			= $_SERVER['HTTP_HOST'];
			$scriptName = $_SERVER['SCRIPT_NAME'];
			$sufix = substr($scriptName, 0, strpos($scriptName, 'index'));
			
			return $requestScheme . '://' . $host . $sufix;
		}
		
		/**
		 * 
		 * @return unknown
		 */
		public static function getHost(){
			return $_SERVER['HTTP_HOST'];
		}
		
		/**
		 * 
		 * @param unknown_type $pFile
		 */
		public static function transformeToUrl($pFile){
			$baseUrl = self::baseUrl();
			$arrFile = explode('/', $pFile);
			$arrFile[0] = $baseUrl;
			$url = implode('/', $arrFile);
			return $url;
		}
		
		/**
		 * função para retirar stop words definidas nesta função
		 */
		function removeStopWords($pSlug) {
			$slug = explode('-', $slug);
			foreach ($slug as $k => $value){
				//lista de Stop Words que serão removidas
				$stopWordsList = 'a,da,para,com,o,as,os,de,pra,um,uma,em';
				$keys = explode(',', $stopWordsList);
				foreach($keys as $wordRemove)
					if($value == $wordRemove)
						unset($slug[$k]);
			}
			return implode('-', $slug);
		}
		
		/**
		 * 
		 * @param unknown_type $pString
		 */
		public static function createSlug($pString){
			$string = self::removeAccent($pString);
			$string = strtolower($string);
			$slug = preg_replace('/ /', '-', $string);
			$slug = self::removeShortWordsFromSlug($slug);
			return $slug;
		}
		
		/**
		 * 
		 * @param unknown_type $pInfoLog
		 * @param unknown_type $pTipo
		 * @param unknown_type $pPrefixo
		 * @return Ambigous <unknown, string>
		 */
		public static function criaLog($pInfoLog, $pTipo = null, $pPrefixo = null){
			//self::debugger($pInfoLog);
			if(is_array($pInfoLog)){
				$dataLog = implode('|', $pInfoLog);
			}
			else 
				$dataLog = $pInfoLog;
			
			$log = 'LOG';
			if($pTipo)
				$log .= ' DE ' . $pTipo; 
			
			if($pPrefixo)
				$log .= ' COM PREFIXO ' . $pPrefixo;
			
			$log .= ' GERADO EM ' . date('d/m/Y') . ' às ' . date('H:i:s') . '. DADOS: ';
			$log .= $dataLog;
			
			return $log;
		}
		
		/**
		 * 
		 * @return Ambigous <string, unknown>
		 */
		public static function getIpAtual(){
			$variables = array(
				'REMOTE_ADDR',
				'HTTP_X_FORWARDED_FOR',
				'HTTP_X_FORWARDED',
				'HTTP_FORWARDED_FOR',
				'HTTP_FORWARDED',
				'HTTP_X_COMING_FROM',
				'HTTP_COMING_FROM',
				'HTTP_CLIENT_IP',
			);
			$return = 'Unknown';
			foreach($variables as $variable){
				if(isset($_SERVER[$variable])){
					$return = $_SERVER[$variable];
					break;
				}
			}
			return $return;
		}
		
		/**
		 * 
		 * @param unknown_type $array
		 */
		public static function debugger($array){
			echo'<pre style="font-size:20px">';
			print_r($array);
			echo'</pre>';
		}
		
		/**
		 * 
		 * @param unknown_type $array
		 */
		public static function zend_dump($array){
			\Zend\Debug\Debug::dump($array);
		}
		
		/**
		 * 
		 * @param unknown_type $array
		 */
		public static function dump($array){
			echo'<pre style="font-size:20px">';
			var_dump($array);
			echo'</pre>';
			die;
		}
		
		/**
		 * 
		 * @param unknown_type $pCnpj
		 */
		public static function validaCNPJ($pCnpj){
			$cnpjValido = true;
			//Etapa 1: Cria um array com apenas os digitos numéricos, isso permite receber o cnpj em diferentes formatos como "00.000.000/0000-00", "00000000000000", "00 000 000 0000 00" etc...
			$j = 0;
			for($i = 0; $i < (strlen($pCnpj)); $i++){
				if(is_numeric($pCnpj[$i])){
					$num[$j] = $pCnpj[$i];
					$j++;
				}
			}
				
			//Etapa 2: Conta os dígitos, um Cnpj válido possui 14 dígitos numéricos.
			if(count($num) != 14)
				$cnpjValido = false;
				
			//Etapa 3: O número 00000000000 embora não seja um cnpj real resultaria um cnpj válido após o calculo dos dígitos verificares e por isso precisa ser filtradas nesta etapa.
			if(
					$num[0] == 0 &&
					$num[1] == 0 &&
					$num[2] == 0 &&
					$num[3] == 0 &&
					$num[4] == 0 &&
					$num[5] == 0 &&
					$num[6] == 0 &&
					$num[7] == 0 &&
					$num[8] == 0 &&
					$num[9] == 0 &&
					$num[10]== 0 &&
					$num[11]== 0
			)
				$cnpjValido = false;
				
			//Etapa 4: Calcula e compara o primeiro dígito verificador.
			else{
				$j = 5;
				for($i = 0; $i < 4; $i++){
					$multiplica[$i] = $num[$i] * $j;
					$j--;
				}
				$soma = array_sum($multiplica);
		
				$j = 9;
				for($i = 4; $i < 12; $i++){
					$multiplica[$i] = $num[$i] * $j;
					$j--;
				}
				$soma = array_sum($multiplica);
		
				$resto = $soma % 11;
				if($resto < 2)
					$dg = 0;
				else
					$dg = 11 - $resto;
		
				if($dg != $num[12])
					$cnpjValido = false;
			}
				
			//Etapa 5: Calcula e compara o segundo dígito verificador.
			if(!isset($cnpjValido)){
				$j = 6;
				for($i = 0; $i < 5; $i++){
					$multiplica[$i] = $num[$i] * $j;
					$j--;
				}
				$soma = array_sum($multiplica);
		
				$j = 9;
				for($i = 5; $i < 13; $i++){
					$multiplica[$i] = $num[$i] * $j;
					$j--;
				}
				$soma = array_sum($multiplica);
					
				$resto = $soma % 11;
				if($resto < 2)
					$dg = 0;
				else
					$dg=11-$resto;
		
				if($dg != $num[13])
					$cnpjValido = false;
				else
					$cnpjValido = true;
			}
				
			//Etapa 6: Retorna o Resultado em um valor booleano.
			return $cnpjValido;
		}
		
		/**
		 * 
		 * @return string
		 */
		public static function mobileDetect(){
			$md = new MobileDetect();
			if($md->isMobile()){
				return self::MOBILE;
			}
			elseif($md->isTablet()){
				return self::TABLET;
			}
			else{
				return self::PC;
			}
		}
		
		/**
		 * 
		 * @return string
		 */
		public static function getOs(){
			$userAgent = @strtolower($_SERVER['HTTP_USER_AGENT']);
			if(preg_match('/' . self::WINDOWS . '/', $userAgent)){
				$plataforma = self::WINDOWS;
			}
			elseif(preg_match('/' . self::LINUX . '/', $userAgent) && !preg_match('/' . self::ANDROID . '/', $userAgent)){
				$plataforma = self::LINUX;
			}
			elseif(preg_match('/' . self::MAC . '/', $userAgent)){
				$plataforma = self::MAC;
			}
			elseif(preg_match('/' . self::ANDROID . '/', $userAgent)){
				$plataforma = self::ANDROID;
			}
			elseif(preg_match('/' . self::IPHONE . '/', $userAgent)){
				$plataforma = self::IPHONE;
			}
			elseif(preg_match('/' . self::IPAD . '/', $userAgent)){
				$plataforma = self::IPAD;
			}
			else{
				$plataforma = self::LINUX;
			}
				
			return $plataforma;
		}
		
		/**
		 * 
		 * @return mixed
		 */
		public static function getIp(){
			$remoteAddr = $_SERVER['REMOTE_ADDR'];
			return str_replace('.', '', $remoteAddr);
		}
		
		/**
		 * 
		 * @return string
		 */
		public static function getHashDevice(){
			$device = self::mobileDetect();
			$os = self::getOs();
			$ip = self::getIp();
			$hashDevice = md5($device . '_' . $os . '_' . $ip);
				
			return $hashDevice;
		}
		
		/**
		 * 
		 * @return string
		 */
		public static function getStringRandomica($pLenght = 10){
			$salt = self::SALTCAS;
			$caracteres 		= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$qtdCaracteres		= strlen($caracteres);
			$stringRandomica	= '';
			for ($offset = 0; $offset < $pLenght; $offset++) {
				$rand = rand(0, $qtdCaracteres - 1);
				$stringRandomica .= $caracteres[$rand];
			}
			
			$stringRandomica .= $salt . time();
			return str_shuffle($stringRandomica);
		}
		
		public static function reverseDate($pDate, $pDelimiter, $pGlue){
			$arrDate 	= explode($pDelimiter, $pDate);
			$reverse 	= array_reverse($arrDate);
			$date 		= implode($pGlue, $reverse); 
			return $date;
		}
		
		public static function reverseDatetime($pDatetime, $pDelimiter, $pGlue, $pShowTime = false){
			$arrDatetime	= explode(" ", $pDatetime);
			$date 			= $arrDatetime[0];
			$time			= $arrDatetime[1];
			$arrDate		= explode($pDelimiter, $date);
			$reverseDate 	= array_reverse($arrDate);
			$date 			= implode($pGlue, $reverseDate);
			if($pShowTime === true)
				$date = $date . ' ' . $time;
			 
			return $date;
		}
		
		public static function diffDateInDays($pDate){
			if($pDate){
				$date 	= preg_replace(array('/-/', '/ /', '/:/'), '', $pDate);
				$day 	= substr($date, 0, 8);
				$today	= date('Ymd'); 
				$diff 	= $today - $day; 
				
				return (int)$diff;
			}
			else
				return (int)0;
		}
		
		public static function renameIdFieldInArray($pArray){
			$arrReturn = array();
			foreach($pArray as $key => $val){
				$prefix = reset(explode('_', $key));
				if($prefix === 'id'){
					$sufix = ucfirst(substr($key, 3)); 						
					$arrReturn[$prefix . $sufix] = $val;				
				}
				else{
					$arrReturn[$key] = $val;
				}
			}
			return $arrReturn;
		}
	}