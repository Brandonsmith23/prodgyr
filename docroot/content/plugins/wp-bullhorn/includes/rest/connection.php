<?php
if(!class_exists('BullhornRestConnection'))
{
    /**
     * Handles connecting to the API through OAUTH
     */
	class BullhornRestConnection
	{
	    var $auth_code;
	    var $access_obj;
	    var $token_obj;
	    
	    /**
	     * Constructor
	     */
	    public function __construct($client_id, $client_secret, $username, $password)
	    {
	        $this->auth_code = $this->get_auth_code($client_id, $username, $password);
	        $this->access_obj = $this->get_acccess_token($this->auth_code, $client_id, $client_secret);
	        $this->token_obj = $this->get_rest_token($this->access_obj->access_token);
	    } // END public function __construct__($client_id, $client_secret, $username, $password)
	    
        /**
         * Get an Authorization Code
         */
        private function get_auth_code($client_id, $username, $password)
        {
            $url = "https://auth.bullhornstaffing.com/oauth/authorize?client_id=%s&response_type=code&action=Login&username=%s&password=%s";
            $url = sprintf($url, $client_id, $username, $password);

            $curl = curl_init($url); 
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120);
            curl_setopt($curl, CURLOPT_TIMEOUT, 120);

            $content = curl_exec( $curl );
            curl_close($curl);

            if(preg_match('#Location: (.*)#', $content, $r))
            {
                $l = trim($r[1]);
                $temp = preg_split("/code=/", $l);
                return $temp[1];
            }
            
            $tos_url = "https://auth.bullhornstaffing.com/oauth/authorize?client_id=%s&response_type=code&username=%s&password=%s";
            $tos_link = sprintf($tos_url, $client_id, $username, $password);
            throw new Exception(sprintf('Could not retrieve Auth Code from Bullhorn. 
                Please make sure you have entered your API credentials correctly and that 
                you have you accepted the <a href="%s" target="_blank">Bullhorn Terms of Service</a>.', $tos_link));
        } // END private function get_auth_code($client_id, $username, $password)
        
        /**
         * Use an authorization token to get an Access Token
         */
        private function get_acccess_token($auth_code, $client_id, $client_secret)
        {
            $url = 'https://auth.bullhornstaffing.com/oauth/token?grant_type=authorization_code&code=%s&client_id=%s&client_secret=%s';
            $url = sprintf($url, $auth_code, $client_id, $client_secret);

            $options = array(
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => array()
            ); 

            $curl = curl_init($url); 
            curl_setopt_array($curl, $options); 
            $content = curl_exec($curl); 

            curl_close($curl);

            return json_decode($content);
        } // END private function get_acccess_token($auth_code, $client_id, $client_secret)
        
        /**
         * Get a REST token using an access token
         */
        private function get_rest_token($access_token)
        {
            $url = 'https://rest.bullhornstaffing.com/rest-services/login?version=*&access_token=%s';
            $url = sprintf($url, $access_token);
            $curl = curl_init($url); 
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $content = curl_exec($curl);
            curl_close($curl);
            return json_decode($content);
        } // END private function get_rest_token($access_token)
        
        public static function test($client_id, $client_secret, $username, $password)
        {
            try
            {
                $bh_rest_connection = new BullhornRestConnection($client_id, $client_secret, $username, $password);
                echo sprintf("AUTH CODE: %s\n", $bh_rest_connection->auth_code);
                echo "Access Obj: \n";
                print_r($bh_rest_connection->access_obj);
                echo "Token Obj: \n";
                print_r($bh_rest_connection->token_obj);
            } 
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
        }
    } // END class BullhornRestConnection
} // END if(!class_exists('BullhornRestConnection'))