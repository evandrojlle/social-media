<?php
	namespace Rest\Library\Email;
	
	use Zend\Mail;
	use Zend\Mail\Message;
	use Zend\Mail\Transport\Smtp as SmtpTransport;
	use Zend\Mail\Transport\SmtpOptions;

	class Email{
		protected $_options_smtp = array();
		
		protected $_mail;
		
		protected $_environment;
		
		protected $_cfg;
		/**
		 * 
		 * @param string $pAssunto
		 * @param string $pEmailPara
		 * @param string $pNomePara
		 */
		public function __construct($pCfg){
			$this->_environment = (!empty(getenv('API_ENV'))) ? getenv('API_ENV') : '';
			$this->_cfg = $pCfg;
			$this->_mail = new Message();
			$this->_mail->addFrom($this->_cfg['email_from'], $this->_cfg['name_from']);
			$this->_mail->setSubject($this->_cfg['subject']);
		}

		public function setTo($pDsEmail, $pDsName = null){
			$this->_mail->addTo($pDsEmail, $pDsName);
		}
		
		public function setCc($pDsEmail, $pDsName = null){
			$this->_mail->addCc($pDsEmail, $pDsName);
		}
		
		public function setCco($pDsEmail, $pDsName = null){
			$this->_mail->addBcc($pDsEmail, $pDsName);
		}
		
		public function setReplyTo($pDsEmail, $pDsName = null){
			$this->_mail->addReplyTo($pDsEmail, $pDsName);
		}
		
		public function setFrom($pDsEmail, $pDsName = null){
			$this->_mail->addFrom($pDsEmail, $pDsName);
		}
		
		public function setContent($pContent){
			$this->_mail->setBody($pContent);
		}
		
		public function setEncoding($pEncoding){
			$this->_mail->setEncoding($pEncoding);
		}
		
		public function setHeaders($pHeaders){
			$this->_mail->setHeaders($pHeaders);
		}
		
		public function setSender($pDsEmail, $pDsNome = null){
			$this->_mail->setSender($pDsEmail, $pDsNome);
		}
		
		public function sendEmail(){
			if($this->_environment == 'development')
				$transport = $this->getSmptTransport();
			else
				$transport = new Mail\Transport\Sendmail();
			
			$transport->send($this->_mail);
		}
		
		public function getSmptTransport(){
			$arrOptions = array(
				'name'				=> $this->_cfg['options']['name'],
				'host'  			=> $this->_cfg['options']['host'],
				'port'  			=> $this->_cfg['options']['port'],
				'connection_class'	=> $this->_cfg['options']['connection_class'],
				'connection_config' => $this->_cfg['options']['connection_config']
			);
			$transport = new SmtpTransport();
			$options   = new SmtpOptions($arrOptions);
			$transport->setOptions($options);
			
			return $transport;
		}
	}