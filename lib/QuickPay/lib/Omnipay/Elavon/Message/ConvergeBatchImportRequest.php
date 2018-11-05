<?php 
    namespace Omnipay\Elavon\Message;

    class ConvergeBatchImportRequest extends ConvergeAbstractRequest
	{
			protected $transactionType = 'ccimport';
			
			public function getData()
		    {
		    	$this->validate('importFile',"responseFile");
		    	 $data = array(
		            'ssl_transaction_type' => $this->transactionType,
		            'ssl_import_file' => $this->getImportFile(),
		            'ssl_response_file' => $this->getResponseFile(),
		            'ssl_result_format' => "ASCII",
		            
		        );
		        return array_merge($this->getBaseData(), $data);
		    }

			public function sendData($data)
		    {
		        $httpResponse = $this->httpClient->post($this->getEndpoint() . '/processBatch.do', null, http_build_query($data))
		            ->setHeader('Content-Type', 'multipart/form-data')
		            ->send();
		        return $this->createResponse($httpResponse->getBody());
		    }
	}