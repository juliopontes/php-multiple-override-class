<?php
class JOverrideFile
{
	private $bufferContent;

	public function __construct($filename)
	{
		$this->bufferContent = file_get_contents($filename);
	}
	
	public function getOriginalClass()
	{
		return $this->findToken('T_CLASS');
	}

	private function findToken($token_name)
	{
		$result = null;
		$tokens = token_get_all($this->bufferContent);
		foreach( $tokens as $key => $token)
		{
			if(is_array($token))
	        {
	        	// Find the class declaration
	        	if (token_name($token[0]) == $token_name)
				{
					// Class name should be in the key+2 position
					$result = $tokens[$key+2][1];
					break;
				}
			}
		}
		
		return $result;
	}
	
	public function getExtendedClass()
	{
		return $this->findToken('T_EXTENDS');
	}

	public function setBuffer($bufferContent)
	{
		$this->bufferContent = $bufferContent;
	}

	public function getBuffer()
	{
		return $this->bufferContent;
	}

	public function load()
	{
		if (!empty($this->bufferContent))
		{
			// Finally we can load the base class
			eval('?>'.$this->bufferContent.PHP_EOL.'?>');
		}
	}
}