<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'wsQueryHelper' ) ) {
class wsQueryHelper {

	private $serviceUri = null;
	private $apikey = null;
	private $urlproxy = null;
	private $useproxy = 0;
	private $usegzip = 1;
	private $formlabel = null;
	public $errmsg = "";
	public $infomsg = "";

	public function __construct($serviceUri, $apikey)
	{
		$this->serviceUri = COM_BOOKINGFORCONNECTOR_WSURL;
		$this->apikey =  COM_BOOKINGFORCONNECTOR_API_KEY;
		$this->formlabel = '';
		$this->useproxy = COM_BOOKINGFORCONNECTOR_USEPROXY;
		$this->urlproxy = COM_BOOKINGFORCONNECTOR_URLPROXY;
		$this->usegzip = 1;
		$this->cachetime = COM_BOOKINGFORCONNECTOR_CACHETIME; // 1 hour default
		$this->cachedir = COM_BOOKINGFORCONNECTOR_CACHEDIR;
	}

	public function addFilter(&$filterbase, $filter, $operator) {
		if (isset($filter)) {
			if ($filterbase !== '')
				$filterbase .= ' ' . $operator . ' ';
			$filterbase .= $filter;
		}
		return $this;
	}

	public function executeQuery($url, $method = 'GET', $setApiKey = true, $skip_cache=TRUE) {

		if (isset($url)) {
			
			if ( ! is_dir($this->cachedir)) {
				mkdir($this->cachedir, 0755, true);
			}
			$hash = md5($url);
			$bfifile = $this->cachedir ."/bfi_$hash.cache";
			
			$mtime = 0;
			if (file_exists($bfifile)) {
				$mtime = filemtime($bfifile);
			}
			$bfifiletimemod = $mtime + $this->cachetime;			
			
			if ($bfifiletimemod < time() || $skip_cache) {
				$body = array();
				$isInPost = false;
				if (isset($method) && strtoupper($method) === "POST" ) {
					$isInPost = true;
					$urlParsed = explode("?",$url);
					$url = $urlParsed[0];
					if ($setApiKey) {
						$url .='?s=' . uniqid('', true) . '&apikey='.$this->apikey;
					}
					if (isset($urlParsed[1])) {
						$body = $urlParsed[1];
					}
				}

				$ch = curl_init($url);

				if($this->useproxy ==1 && !empty($this->urlproxy)){
					curl_setopt($ch, CURLOPT_PROXY, $this->urlproxy);
				}
				if($this->usegzip ==1){
					curl_setopt($ch,CURLOPT_ENCODING,'gzip');
				}

				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPGET, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				if ($isInPost){
					curl_setopt ($ch, CURLOPT_POST, true);
					curl_setopt ($ch, CURLOPT_POSTFIELDS, $body);
				}
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
				curl_setopt($ch,CURLOPT_TIMEOUT,360);
				curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,0);

				$http_codes = parse_ini_file("httpcode.ini");
				$r = curl_exec($ch);
				if(!curl_errno($ch)) {
					$info = curl_getinfo($ch);
					$this->infomsg = 'Took ' . $info['total_time'] . ' seconds to send a request <!-- to ' . $info['url'] . ' -->' ;
					if ($info['http_code'] >= 500 && $info['http_code'] <600){
						$this->errmsg = $http_codes[$info['http_code']];
					}
					if ($info['http_code'] >= 400 && $info['http_code'] <500){
						$this->errmsg = $http_codes[$info['http_code']];
					}
	//				echo "<pre>";
	//				echo print_r($info);
	//				echo "</pre>";
					
	//				 echo '<!--Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'] . '-->';
					if ($r && !$skip_cache) {
						file_put_contents($bfifile, $r);
					}
				}else {
//				echo '<!--Curl error: ' . curl_error($ch) . '-->';
				$this->errmsg = curl_error($ch);
					if($this->useproxy ==1 && !empty($this->urlproxy)){
						$this->errmsg .= " ;proxy enabled: check proxy ";
					 }
				 }
			} else {
				$r = file_get_contents($bfifile);
			}
			return $r;
		}
		return null;
	}

	public function getQuery($options = array()) {
		$url = $this->serviceUri;

		if (isset($options['path']))
		{
			$url .= $options['path'];
		} else {
			return null;
		}

		if (!isset($options['data'])) {
			return $url;
		} else {
			$options['data']['apikey'] = $this->apikey;
		}

		$options['data'] = $this->sanitizeData($options['data']);
		$query = http_build_query($options['data']);

		// http_build_query has urlencoded the query and char '$' has been replaced by '%24'. This restores the '$' char
		$query = str_ireplace('%24', '$', $query);
		$query = str_ireplace('%27', '\'', $query);
		$query = str_ireplace('__27__', '\'\'', $query);
		$query = str_ireplace('__1013__', '%0D%0A', $query);
		if (stripos($url,'?') === false) {
			$url .= '?';
		} else {
			$url .= '&';
		}

		$url .= $query;

		return $url;
	}

	public function sanitizeData($data) {
		$newData = array();
		$matches = array();
		foreach ($data as $key=>$elem) {
			if(!empty($elem)){
				$elem = str_replace("\n\r", "__1013__", $elem);
				$elem = str_replace("\n", "__1013__", $elem);
				$elem = str_replace("\r", "__1013__", $elem);
			}
			if (preg_match("/^\'(.*?)\'$/i", $elem, $matches) > 0) {
				$newData[$key] = "'" . str_ireplace('\'','__27__',$matches[1]) . "'";
			} else {
				$newData[$key] = $elem;
			}
		}
		return $newData;
	}
	public function url_exists($url = "") {
		if(empty($url)){
			$url = $this->serviceUri;
		}
		if (!$fp = curl_init($url)) return false;
		return true;
	}		

}
}