<?php
class Pushover
{
	/*
	 * Pushover json api service url
	 */
	const C_API_URL = 'https://api.pushover.net/1/messages.json';

	/**
	 * Properties storage array
	 * @var array
	 */
	private $fProperties;

	/**
	 * cURL instance
	 */
	private $fCurl;

	//--------------------------------------------------------------------------//

	/**
	 * Properties getter
	 * @param string $aPropertyName Property name
	 * @return mixed
	 */
	public function __get($aPropertyName)
	{
		if(array_key_exists($aPropertyName, $this->fProperties))
			return $this->fProperties[$aPropertyName];
		return null;
	}

	/**
	 * Properties setter
	 * @param string $aPropertyName Property name
	 * @param mixed $aValue Property value
	 */
	public function __set($aPropertyName, $aValue)
	{
		$this->fProperties[$aPropertyName] = $aValue;
	}

	//--------------------------------------------------------------------------//

	/**
	 * Class constructor
	 * @param string $aApplicationToken Application token
	 */
	public function __construct($aApplicationToken = null)
	{
		if(!empty($aApplicationToken))
			$this->applicationToken = $aApplicationToken;

		$this->fCurl = curl_init();
		curl_setopt($this->fCurl, CURLOPT_URL,            self::C_API_URL);
		curl_setopt($this->fCurl, CURLOPT_HEADER,         false);
		curl_setopt($this->fCurl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->fCurl, CURLOPT_SSL_VERIFYPEER, false);		
	}

	/**
	 * Class destructor
	 */
	public function __destruct()
	{
		curl_close($this->fCurl);
	}

	//--------------------------------------------------------------------------//

	/**
	 * Throws an exceprion with single message
	 * @param mixed $aMessage
	 * @throws PushoverException
	 */
	public function throwMessage($aMessage)
	{
		throw new PushoverException(array($aMessage));
	}

	/**
	 * Throws an exceprion with an array of messages
	 * @param array $aMessages
	 * @throws PushoverException
	 */
	public function throwMessages(array $aMessages)
	{
		throw new PushoverException($aMessages);
	}

	//--------------------------------------------------------------------------//

	/**
	 * Send pushover notification
	 */
	public function send()
	{
		if(!strlen($this->applicationToken))
			$this->throwMessage('Application token is empty');
		if(!strlen($this->userToken))
			$this->throwMessage('User token is empty');
		if(!strlen($this->notificationMessage))
			$this->throwMessage('Notification message is empty');

		if(intval($this->notificationTimestamp) <= 0)
			$this->notificationTimestamp = time();

		$lSendParams = array(
			'token'     => $this->applicationToken,
			'user'      => $this->userToken,
			'device'    => $this->userDevice,
			'title'     => $this->notificationTitle,
			'message'   => $this->notificationMessage,
			'priority'  => $this->notificationPriority,
			'timestamp' => $this->notificationTimestamp,
			'url'       => $this->notificationUrl,
			'url_title' => $this->notificationUrlTitle
		);

		foreach($lSendParams as $lKey => $lParam)
			if(empty($lParam))
				unset($lSendParams[$lKey]);

		curl_setopt($this->fCurl, CURLOPT_POSTFIELDS, $lSendParams);
		$lResponseJson = curl_exec($this->fCurl);

		if($lResponseJson === false)
			$this->throwMessage('API request error');

		$lResponse = json_decode($lResponseJson, true);

		if(empty($lResponse) || !is_array($lResponse))
			$this->throwMessage('Bad API response');

		if(!empty($lResponse['errors']))
			$this->throwMessages($lResponse['errors']);
		if(empty($lResponse['status']) || intval($lResponse['status']) != 1)
			$this->throwMessage('Unknown notification send error');
		
	}
}



class PushoverException extends Exception
{
	/**
	 * Messages array
	 * @var array
	 */
	private $fMessages;

	/**
	 * Exception constructor
	 * @param array $aMessages An array of messages
	 */
	public function __construct(array $aMessages)
	{
		parent::__construct('PushoverException exception');
		$this->fMessages = $aMessages;
	}

	/**
	 * Get messages array
	 * @return array
	 */
	public function getMessages()
	{
		return empty($this->fMessages) ? array() : $this->fMessages;
	}
}