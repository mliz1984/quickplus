<?php
namespace Quickplus\Lib;
class QuickFreshdesk {
    private $domain = '';
    private $apiKey = '';
    private $password = '';
    private $lastHttpStatusCode = 200;
    private $lastHttpResponseText = '';
    private $proxyServer = "";
    protected $debug = false;
    function __construct($domain, $apiKey, $password = 'X') {
        $strippedDomain = preg_replace('#^https?://#', '', $domain); // removes http:// or https://
        $strippedDomain = preg_replace('#/#', '', $strippedDomain); // get trailing slash
        $this->domain = $strippedDomain;
        $this->password = $password;
        $this->apiKey = $apiKey;
    }
    
    public function setDebug($debug)
    {
        $this->debug  = $debug;
    }
     
    private function restCall($urlMinusDomain, $method, $postData = '') {
        $url = "https://{$this->domain}$urlMinusDomain";
        $header[] = "Content-type: application/json";
        $ch = curl_init ($url);

        if( $method == "POST") {
            if( empty($postData) ){
                $header[] = "Content-length: 0"; // <-- seems to be unneccessary to specify this... curl does it automatically
            }
            curl_setopt ($ch, CURLOPT_POST, true);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postData);
        }
        else if( $method == "PUT" ) {
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "PUT" );
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postData);
        }
        else if( $method == "DELETE" ) {
            curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "DELETE" ); // UNTESTED!
        }
        else {
            curl_setopt ($ch, CURLOPT_POST, false);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->apiKey}:{$this->password}");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        if( !empty($this->proxyServer) ) {
            curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888');
        }
        $verbose = ''; // set later...
        if( $this->debug ) {
            // CURLOPT_VERBOSE: TRUE to output verbose information. Writes output to STDERR,
            // or the file specified using CURLOPT_STDERR.
            echo $url."<br>"; 
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $verbose = fopen('php://temp', 'rw+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }
        $httpResponse = curl_exec ($ch);
        if($this->debug ) {
            !rewind($verbose);
            $verboseLog = stream_get_contents($verbose);
            print $verboseLog;
        }
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //curl_close($http);
        if( !preg_match( '/2\d\d/', $http_status ) ) {
            //print "ERROR: HTTP Status Code == " . $http_status . " (302 also isn't an error)\n";
        }
        // print "\n\nREST RESPONSE: " . $httpResponse . "\n\n";
        $this->lastHttpResponseText = $httpResponse;
        return $httpResponse;
    }
   
    public function getLastHttpStatus() {
        return $this->lastHttpStatusCode;
    }
   
    public function getLastHttpResponseText() {
        return $this->lastHttpResponseText;
    }
  
    public function setProxyServer($proxyServer)
    {
        $this->proxyServer = $proxyServer;
    }  
   
    public function getAllTickets($page=1,$perpage=20,$where=null) {
        $apiUrl = "/api/v2/tickets?";
        if($where!=null&&trim($where)!="")
        {
            $apiUrl.=$where;
        }
        if($perpage!=0&&$page>0)
        {
            $apiUrl.="&per_page=".$perpage."&page=".$page;
        }
        $apiUrl.="&include=requester,stats&order_by=created_at&order_type=desc";

        $json = $this->restCall($apiUrl, "GET");
        if( empty($json) ) {
            return FALSE;
        }
        $json = json_decode(json_encode(json_decode($json)),true);
        return $json;
    }
    
  
    public function getSingleTicket($ticketId) {
        $json = $this->restCall("/api/v2/tickets/$ticketId", "GET");
        
        if( empty($json) ) {
            return FALSE;
        }
        $json = json_decode(json_encode(json_decode($json)),true);
        return $json;
    }
    
    
    
    public function getTicketsByEmail($page=1,$perpage=20,$email) {
       return $this->getAllTickets($page,$perpage,"email=".$email);
    }


    
   
    public function getTicketFields() {
        $json = $this->restCall("/api/v2/ticket_fields", "GET");
        if( empty($json) ) {
            return FALSE;
        }
        $json = json_decode(json_encode(json_decode($json)),true);
        return $json;
    }

    public function createTicket($name,$email,$subject,$description,$status=2,$priority=1)
    {
        $json = $this->restCall("/api/v2/tickets", "POST",json_encode(Array("name"=>$name,"email"=>$email,"subject"=>$subject,"description"=>$description,"status"=>$status,"priority"=>$priority)));
        $json = json_decode(json_encode(json_decode($json)),true);
        return $json;
    }
   
    public function getAllContacts($page=1,$pagesize=20,$query=null)
    {
        $apiUrl = "/api/v2/contacts?page=$page&per_page=$pagesize";
        if($query!=null&&trim($query)!="")
        {
            $apiUrl = "&query=\"".$query."\"";
        }
        $json = $this->restCall($apiUrl, "GET");
        if( empty($json) ) {
            return FALSE;
        }
        $json = json_decode(json_encode(json_decode($json)),true);
        return $json;
    }

    public function viewContact($id)
    {
        $apiUrl = "/api/v2/contacts/$id";
        $json = $this->restCall($apiUrl, "GET");
        if( empty($json) ) {
            return FALSE;
        }
        $json = json_decode(json_encode(json_decode($json)),true);
        return $json;
    }
    
    public function updateContact($id,$fields,$customFields=null)
    {
         if(!is_array($fields))
         {
             $fields = Array();
         }
            if(is_array($customFields)&&count($customFields)>0)
            {
                    $fields["custom_fields"] = $customFields;
            }
        
         $json = $this->restCall("/api/v2/contacts/$id", "PUT",json_encode($fields));
         $json = json_decode(json_encode(json_decode($json)),true);
         return $json;
           
    }
}
?>