<?php
//http://framework.zend.com/manual/en/zend.filter.set.html
// TODO: define Kp_Application
class Kp_Crypt
{
	protected $td;
	protected $error_handler;
	protected $initialization_vector;
	protected $key;
	protected $use_mcrypt = true;
	protected $pcrypt = false;
	
	//function DataEncrypter($in_error_handler, $key = 'ThisKeyIsF0rS1mplyh1r3d98ew120dk3ds4klls0xc', $initialization_vector = false, $algorithm = 'blowfish', $mode = 'ecb')
        public function __constructor($in_error_handler, $key = 'ThisKeyIsF0rS1mplyh1r3d98ew120dk3ds4klls0xc', $initialization_vector = false, $algorithm = 'blowfish', $mode = 'ecb')
	{
		$this->error_handler = $in_error_handler;
		
		if ($this->use_mcrypt)
		{
			$this->_internal_initializeMCrypt($key, $initialization_vector, $algorithm, $mode);
		}
		else
		{
			$this->_internal_initializePCrypt($key);
		}
	}

	public function initialize()
	{
		// initialize mcrypt library with mode/cipher, encryption key, and random initialization vector
		if ($this->use_mcrypt)
		{
			mcrypt_generic_init($this->td, $this->key, $this->initialization_vector);		
		}
	}
	
	public function uninitialize()
	{
		if ($this->use_mcrypt)
		{
			mcrypt_generic_deinit($this->td);  //Remove for now to avoid a fatal error on Windows
		}
	}
	
	public function encrypt($plain_string)
	{
		$this->initialize();
		if ($this->use_mcrypt)
		{
			$encrypted_string = $this->_internal_encryptWithMCrypt($plain_string);
		}
		else
		{
			$encrypted_string = $this->_internal_encryptWithPCrypt($plain_string);
		}
		$this->uninitialize();		
		return $encrypted_string;
	}
		
	public function decrypt($encrypted_string)
	{
		$this->initialize();
		if ($this->use_mcrypt)
		{
			$decrypted_string = $this->_internal_decryptWithMCrpyt($encrypted_string);
		}
		else
		{
			$decrypted_string = $this->_internal_decryptWithPCrypt($encrypted_string);
		}
		$this->uninitialize();		
		return $decrypted_string;
	}

	public function __destruct()
	{
		// shutdown mcrypt
		//mcrypt_generic_deinit($this->td);

		// close mcrypt cipher module
		if (is_resource($this->td)) {
                    mcrypt_module_close($this->td);
		}
	}
		
	/** PCrypt Functions **/
	protected function _internal_encryptWithPCrypt($plain_string)
	{
		$encrypted_result = base64_encode($this->pcrypt->encrypt($plain_string));
		return $encrypted_result;
	}
	
	protected function _internal_decryptWithPCrypt($encrypted_string)
	{
		$decrypted_result = trim($this->pcrypt->decrypt(base64_decode($encrypted_string)));
		return $decrypted_result;
	}
	
	protected function _internal_initializePCrypt($in_key)
	{
		$in_key = substr(md5($in_key), 0, 56);		
		$this->pcrypt = new Kp_Crypt_Pcrypt(MODE_ECB, "BLOWFISH", $in_key);
	}


	/** MCrypt Functions **/
	protected function _internal_encryptWithMCrypt($plain_string)
	{
		
		//encrypt string using mcrypt and then encode any special characters
		//and then return the encrypted string
				
		$encrypted_result = base64_encode(mcrypt_generic($this->td, $plain_string));
		return $encrypted_result;
	}
	
	protected function _internal_decryptWithMCrpyt($encrypted_string)
	{
		//remove any special characters then decrypt string using mcrypt and then trim null padding
		//and then finally return the encrypted string
		$decrypted_result = trim(mdecrypt_generic($this->td, base64_decode($encrypted_string)));
		return $decrypted_result;		
	}
	
	protected function _internal_initializeMCrypt($key, $initialization_vector, $algorithm, $mode)
	{
		if(extension_loaded('mcrypt') === FALSE)
		{
			$prefix = (PHP_SHLIB_SUFFIX == 'dll') ? 'php_' : '';
			if (dl($prefix . 'mcrypt.' . PHP_SHLIB_SUFFIX) == FALSE)
			{
				$this->error_handler->handleLogicError("data_encrypter", $this->error_handler->ERROR_FATAL, "loading mcrypt", "Could not load the MCrypt module.");
				die('The Mcrypt module could not be loaded.');
			}
		}

		if($mode != 'ecb' && $initialization_vector === false)
		{
			//the iv must remain the same from encryption to decryption and is usually
			//passed into the encrypted string in some form, but not always.
			
			$this->error_handler->handleLogicError("data_encrypter", $this->error_handler->ERROR_FATAL, "initializing mcrypt", "An initialization vector must be provided");
			die('In order to use encryption modes other then ecb, you must specify a unique and consistent initialization vector.');
		}

		// set mcrypt mode and cipher
		$this->td = mcrypt_module_open($algorithm, '', $mode, '') ;

		// Unix has better pseudo random number generator then mcrypt, so if it is available lets use it!
		$random_seed = strstr(PHP_OS, "WIN") ? MCRYPT_RAND : MCRYPT_DEV_URANDOM;

		// if initialization vector set in constructor use it else, generate from random seed
		$initialization_vector_size = mcrypt_enc_get_iv_size($this->td);
		$this->initialization_vector = ($initialization_vector === false) ? mcrypt_create_iv($initialization_vector_size, $random_seed) : substr($initialization_vector, 0, $initialization_vector_size);

		// get the expected key size based on mode and cipher
		$expected_key_size = mcrypt_enc_get_key_size($this->td);

		// we dont need to know the real key, we just need to be able to confirm a hashed version
		$this->key = substr(md5($key), 0, $expected_key_size);		
	}
}