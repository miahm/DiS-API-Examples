<?php

    /** 
     * A utility Function to make calls to the API using PHP
     * 
     * @param   $url        String          The URL to call
     * @param   $fields     Array           Array of name=>valye pairs
     * @param   $method     String          'GET', 'POST', 'PUT' ...
     * @param   $headers    Array           Array of Header strings
     *
     * Returns  The response object
     */
    function cUrl($url, $fields = null, $method = 'GET', $headers = array())
    {
        // Set up some initial variables
        $return = new stdClass();           //Object to store in a variable that will be returned by this function
        $curl_info =  null;                 //To store cURL details
        $curl_error = null;                 //To store cURL errors
        
        // If no HTTP header was sent, set it to a blank array
        if (!is_array($headers)) $headers = array();
        
        // Before we make any calls to the API, we need to set up the Authorization Token
        // We'll send the Token as an "Authorization" header. Format is "Bearer <token>"
        // Now, set the authorization header
        if ( isset($fields['token']) ) {
        
            $headers = array_merge($headers, array('Authorization: Bearer ' . $fields['token']));
            
        }
        
        // Initialize a cURL object
        $ch = curl_init();
    
        // Set the target URL
        curl_setopt($ch, CURLOPT_URL, $url);
    
        // Tell curl to use HTTP method specified in the by the caller
        if ( $method != 'GET' ) {
            
            // Set method
            curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $method);
        
            // Tell curl that this is the body of the POST
            // String data formatted as Query Parameters - key=value pairs 
            if ( $fields )  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            
        }
    
        // Set the Headers
        if (count($headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        // Tell curl not to return headers, but do return the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        
        // Set return object to the cURL response
        $response = curl_exec($ch);
        
        //Get the cURL info and errors, if any
        $curl_info =  curl_getinfo ($ch);
        $curl_error =  curl_error ($ch);
        
        // Custom function here for handing errors 
        // .... Call you custom function here 
        
        // You can check HTTP code of the resoponse
        $http_code = $this->curl_info['http_code'];
        
        if ( $response == null || $curl_error || $http_code == 401){
            
            // Dealing with expired token
            // http code 401 = issues with authentication
            // Auth token was in fact supplied
            if ( $http_code == 401 && isset($fields['token']) && !empty($fields['token']) ){       
                
                //See if we have messages to confirm an authentication failure
                if ( isset($response['response']['messages']) && is_array($response['response']['messages']) ){
                    
                    foreach ( $response['response']['messages'] as $msg ) {
                        
                        if ( $msg == 'Authentication Failure' ) {
                            
                            $authURL = "";
                            
                            $response = $this->cUrl($URL, $fields, $method, $headers );
                            
                        }
                    
                    }
                
                }
                
            }
            
            // ... handle exception
            
            
        }
        
        
        curl_close($ch);
        
        return $return;
    }
    
    