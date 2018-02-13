<?php
use Thrift\ClassLoader\ThriftClassLoader;
use Thrift\Protocol\TBinaryProtocol;
use Thrift\Protocol\TMultiplexedProtocol;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;


class thrift{
	var $client;
	function __construct($client = '',$ip = '',$port = 9090){
		if($ip 		== '') 	exit('Thrift host error~!');
		if($client 	== '') 	exit('Client error~!');
		include_once 		__DIR__.'/Thrift/ClassLoader/ThriftClassLoader.php';
		$loader 	= new ThriftClassLoader();
		$loader->registerNamespace('Thrift', __DIR__ );
		$loader->register();
		$socket 		= new TSocket($ip, $port);
		$transport 		= new TFramedTransport($socket, 1024, 1024);
		$protocol		= new TBinaryProtocol($transport);
		//加载gen-php文件
		$GEN_DIR 	= 'application/config/'.$client;
		$client_file = $GEN_DIR.'/'.$client.'.php';
		if(!file_exists($client_file)) exit('File not exist:'.$client_file);
		include_once $client_file;
		$client_name 		= $client.'Client';
		$this->client 		= new $client_name($protocol);
		$this->transport 	= $transport; 
		spl_autoload_register('__autoload');
	}
	//获取thrift方法
	function call($method = '',$var = array()){
		if($method=='' or !method_exists($this->client,$method)) exit('Method error~!');
		$this->transport->open();
		$return  = call_user_func_array(array($this->client,$method),$var);
		$this->transport->close();
		return $return;
	}
}

class thrift_multiplexed{
	var $client;
	function __construct($client = '',$namespace='',$service_name='',$ip = '',$port = 9090){
		if($ip 		== '') 	exit('Thrift host error~!');
		if($client 	== '') 	exit('Client error~!');
		include_once 		__DIR__.'/Thrift/ClassLoader/ThriftClassLoader.php';
		$loader 	= new ThriftClassLoader();
		$loader->registerNamespace('Thrift', __DIR__ );
		$loader->register();
		$socket 		= new TSocket($ip, $port);
                $socket->setRecvTimeout(5000000);
		$transport 		= new TBufferedTransport($socket);
		$protocol		= new TMultiplexedProtocol(new TBinaryProtocol($transport),$service_name);
		//加载gen-php文件
		$GEN_DIR 	= CMS_ROOT.DIRECTORY_SEPARATOR.APPLICATION.'/config/gen-php';
		$client_file = $GEN_DIR.'/'.$namespace.'/'.$client.'.php';
		if(!file_exists($client_file)) exit('File not exist:'.$client_file);
		include_once $client_file;
                include_once $GEN_DIR.'/'.$namespace.'/Types.php';
		$client_name 		= $namespace."\\".$service_name.'Client';
		$this->client 		= new $client_name($protocol);
		$this->transport 	= $transport; 
		spl_autoload_register('__autoload');
	}
	//获取thrift方法
	function call($method = '',$var = array()){
		if($method=='' or !method_exists($this->client,$method)) exit('Method error~!');
		$this->transport->open();
		$return  = call_user_func_array(array($this->client,$method),$var);
		$this->transport->close();
		return $return;
	}
	
}