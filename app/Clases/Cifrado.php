<?php
/**
 * cifrado
 * Agrega capacidad de crear hash SHA1SUM para LDAP, NTLM y LMHash y cifrado
 * @author <varius>
 */

 namespace App\Clases;
ini_set('default_charset','UTF-8');

class Cifrado {
    function __construct(){
        $this->ciphering = 'aes-256-ctr';
        $this->iv_length = 16;
        $this->hash_algo = 'sha256';
        $this->options = OPENSSL_RAW_DATA;
    }

    /**
     * Tomando una contraseña dada,
     * regresa una cadena cifrada para el atributo en userPassword
     * @param string $Input
     * @return string
     */
    function slappasswd($Input){
        $lpass = "{SHA}" . base64_encode( pack( "H*", sha1($Input) ) );
        return $lpass;
    }

    /**
     * Tomando una contraseña dada,
     * Regresa un hash para el atributo sambaNTPassword
     * @param string $Input
     * @return string
     */
    function NTLMHash($Input) {
        // Convert the password from UTF8 to UTF16 (little endian)
        $Input=iconv('UTF-8','UTF-16LE',$Input);

        // Encrypt it with the MD4 hash
        $MD4Hash=bin2hex(mhash(MHASH_MD4,$Input));
        // You could use this instead, but mhash works on PHP 4 and 5 or above
        // The hash function only works on 5 or above
        //  $MD4Hash=hash('md4',$Input);

        // Make it uppercase, not necessary, but it's common to do so with NTLM hashes
        $NTLMHash=strtoupper($MD4Hash);

        // Return the result
        return($NTLMHash);
    }	

    /**
     * Tomando una contraseña dada,
     * Regresa un hash para el atributo sambaLMPassword 
     * @param string $string
     * @return string
     */
    function LMhash($string){
        $string = strtoupper(substr($string,0,14));

        $p1 = $this->LMhash_DESencrypt(substr($string, 0, 7));
        $p2 = $this->LMhash_DESencrypt(substr($string, 7, 7));

        return strtoupper($p1.$p2);
    }

    /**
     * Función auxiliar para LMhash. En realidad, no se que devuelve por si misma 
     * @param string $string
     * @return string
     */
    function LMhash_DESencrypt($string){
        $key = array();
        $tmp = array();
        $len = strlen($string);

        for ($i=0; $i<7; ++$i){
            $tmp[] = $i < $len ? ord($string[$i]) : 0;
        }

        $key[] = $tmp[0] & 254;
        $key[] = ($tmp[0] << 7) | ($tmp[1] >> 1);
        $key[] = ($tmp[1] << 6) | ($tmp[2] >> 2);
        $key[] = ($tmp[2] << 5) | ($tmp[3] >> 3);
        $key[] = ($tmp[3] << 4) | ($tmp[4] >> 4);
        $key[] = ($tmp[4] << 3) | ($tmp[5] >> 5);
        $key[] = ($tmp[5] << 2) | ($tmp[6] >> 6);
        $key[] = $tmp[6] << 1;

        $is = mcrypt_get_iv_size(MCRYPT_DES, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($is, MCRYPT_RAND);
        $key0 = "";

        foreach ($key as $k){
            $key0 .= chr($k);        
        }
        $crypt = mcrypt_encrypt(MCRYPT_DES, $key0, "KGS!@#$%", MCRYPT_MODE_ECB, $iv);

        return bin2hex($crypt);
    }

    /**
     * Cifra $contenido tomando como clave a $clave
     * con algunos parametros.
     * @param string $text 
     * @param string $key
     * @return string
     */
    function encrypt($contenido, $clave) {
        $text_num =str_split($text,  $this->bit_check);
        $text_num = $this->bit_check-strlen($text_num[count($text_num)-1]);
        for ($i=0;$i<$text_num; $i++) {
            $text = $text . chr($text_num);
        }
        $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES,'','cbc','');
        mcrypt_generic_init($cipher, $key, $this->iv);
        $decrypted = mcrypt_generic($cipher,$text);
        mcrypt_generic_deinit($cipher);
        return base64_encode($decrypted);
    }
  
    /**
     * Cifra $contenido considerando a $clave, según parametros datos en __construct
     * @param String $contenido 
     * @param String $clave
     */
    public function cifrar($contenido, $clave){
        $iv = random_bytes($this->iv_length);
        $encryption_key = openssl_digest($clave, $this->hash_algo, TRUE);

        $encryption = openssl_encrypt($contenido, $this->ciphering, $encryption_key, $this->options, $iv);
        return base64_encode($iv . $encryption);
    }

    /**
     * Descifra $contenido considerando a $clave, según parametros datos en __construct
     * @param String $contenido 
     * @param String $clave
     */
    public function descifrar($contenido, $clave) {
        $raw = base64_decode($contenido);
        $iv = substr($raw, 0, $this->iv_length);
        $encryption_key = openssl_digest($clave, $this->hash_algo, TRUE);

        $contenido = substr($raw, $this->iv_length);
        return openssl_decrypt($contenido, $this->ciphering, $encryption_key, $this->options, $iv);
    }

    
}

