<?php
if(!class_exists('BullhornEntity'))
{
	class BullhornEntity
	{
	    public $connection = NULL;
	    public $type = NULL;
	    
	    /**
	     * Constructor
	     */
	    public function __construct($connection, $type)
	    {
	        $this->connection = $connection;
	        $this->type = $type;
	    }
	    
	    /**
	     * Get a Single Entity instance
	     */
	    public function get($id, $fields)
	    {
            // Build the query URL
            $url = sprintf(
                '%sentity/%s/%s?fields=%s&BhRestToken=%s', 
                $this->connection->token_obj->restUrl, 
                $this->type,
                $id,
                $fields,
                $this->connection->token_obj->BhRestToken
            );
            
            // Run the CURL request
            $curl = curl_init($url); 
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($curl);
            curl_close($curl);
            
            // Decode the Response
            $content = json_decode($content);
            if(!empty($content->errorMessage))
            {
                throw new Exception(sprintf("GET URL:\n%s\n\nQuery Failed: %s", $url, $content->errorMessage));
            }
            
            return $content;
	    } // END public function get($id, $fields)
	    
	    /**
	     * A Query Operation
	     */
	    public function query($fields, $where)
	    {
	        $fields = urlencode($fields); // "*" or "id"
	        $where = urlencode($where); // "isDeleted = false AND isOpen = true AND isOpen = true"
            $start = 0;
            
            $data = array();
            do
            {
                // Build the query URL
                $url = sprintf(
                    '%squery/%s?fields=%s&where=%s&BhRestToken=%s&start=%s', 
                    $this->connection->token_obj->restUrl, 
                    $this->type, 
                    $fields, 
                    $where, 
                    $this->connection->token_obj->BhRestToken,
                    $start
                );

                // Run the CURL request
                $curl = curl_init($url); 
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $content = curl_exec($curl);
                curl_close($curl);
                
                // Decode the Response
                $content = json_decode($content);
                if(!empty($content->errorMessage))
                {
                    throw new Exception(sprintf("Query URL:\n%s\n\nQuery Failed: %s", $url, $content->errorMessage));
                }
                // Pull out the entities
                //var_export($content);
                $data = array_merge($data, $content->data);

                // Move to the next 15 entities
                $start += 15;
            }
            while($content->count == 15);
            
            return $data;
	    } // END public function query($fields, $where)
	    
	    /**
	     * A Search Operation
	     */
	    public function search($fields, $where)
	    {
	        $fields = urlencode($fields); // "*" or "id"
	        $where = urlencode($where); // "isDeleted = false AND isOpen = true AND isOpen = true"
            $start = 0;
            
            $data = array();
            do
            {
                // Build the query URL
                $url = sprintf(
                    '%ssearch/%s?fields=%s%s&BhRestToken=%s&start=%s&count=15', 
                    $this->connection->token_obj->restUrl, 
                    $this->type, 
                    $fields, 
                    (!empty($where) ? sprintf("&query=%s", $where) : ''), 
                    $this->connection->token_obj->BhRestToken,
                    $start
                );
            
                // Run the CURL request
                $curl = curl_init($url); 
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $content = curl_exec($curl);
                curl_close($curl);
                
                // Decode the Response
                $content = json_decode($content);
                if(!empty($content->errorMessage))
                {
                    throw new Exception(sprintf("Query URL:\n%s\n\nQuery Failed: %s", $url, $content->errorMessage));
                }
                // Pull out the entities
                //var_export($content);
                $data = array_merge($data, $content->data);

                // Move to the next 15 entities
                $start += 15;
            }
            while($content->count == 15);
            
            return $data;
	    } // END public function search($fields, $where)
	    
	    /**
	     * The REST API uses POST for Entity Update
	     */
	    public function update($id, $obj)
	    {
            // Build the query URL
            $url = sprintf(
                '%sentity/%s/%s?BhRestToken=%s', 
                $this->connection->token_obj->restUrl, 
                $this->type,
                $id,
                $this->connection->token_obj->BhRestToken
            );

            $data_string = json_encode($obj); 

            $curl = curl_init($url); 
            curl_setopt($curl, CURLOPT_URL, $url); 
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
            );

            $content = curl_exec($curl);
            $content = json_decode($content);
            
            curl_close($curl);
            
            return $content;
            
	    } // END public function update($id, $obj)
	    
	    /**
	     * The REST API uses PUT for Entity Create
	     */
	    public function create($obj)
	    {
            // Build the query URL
            $url = sprintf(
                '%sentity/%s?BhRestToken=%s', 
                $this->connection->token_obj->restUrl, 
                $this->type,
                $this->connection->token_obj->BhRestToken
            );

            // Create the payload
            $put_string = json_encode($obj);
            $put_file = tmpfile();
            fwrite($put_file, $put_string);
            fseek($put_file, 0);

            // Do the CURL Request
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_PUT, true);
            curl_setopt($curl, CURLOPT_INFILE, $put_file);
            curl_setopt($curl, CURLOPT_INFILESIZE, strlen($put_string));

            $content = curl_exec($curl);
            $content = json_decode($content);
            
            curl_close($curl);
            fclose($put_file);
            
            return $content;
	    } // END public function create($obj)
	    
	    /**
	     * Adds a file to an entity
	     */
	    public function add_file($id, $obj)
	    {
	        // Build the query URL
            $url = sprintf(
                '%sfile/%s/%s?BhRestToken=%s', 
                $this->connection->token_obj->restUrl, 
                $this->type,
                $id,
                $this->connection->token_obj->BhRestToken
            );
            
            // Create the payload
            $put_string = json_encode($obj);
            $put_file = tmpfile();
            fwrite($put_file, $put_string);
            fseek($put_file, 0);

            // Do the CURL Request
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_PUT, true);
            curl_setopt($curl, CURLOPT_INFILE, $put_file);
            curl_setopt($curl, CURLOPT_INFILESIZE, strlen($put_string));

            $content = curl_exec($curl);
            $content = json_decode($content);

            curl_close($curl);
            fclose($put_file);
            
            return $content;
	    }
    } // END class BullhornEntity
} // END if(!class_exists('BullhornEntity'))
