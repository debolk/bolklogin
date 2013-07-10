<?php
class ResourceHelper
{
    static function OutputToResponse($code) {
        ob_start();

        $code();
        
        //Convert directly sent headers to response object
        $headers = array();
        foreach(headers_list() as $header)
        {
            $parts = explode(':', $header, 2);
            $headers[strtolower($parts[0])] = $parts[1];
        }
        
        $body = ob_get_contents();
        $result = new Tonic\Response(http_response_code(), $body, $headers);

        ob_end_clean();
        return $result;
    }
}
