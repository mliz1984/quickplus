<?php
namespace Quickplus\Lib;
class QuickProxyConfig
{
	protected $isWebAuth = true;
	protected $webAuthUserName = "";
	protected $webAuthPassword = "";
	protected $seed = "QuickProxySeed";
	protected $tokenExpried = true;
	protected $tokenExpriedTime = 60;
	protected $dataStoreInHeader = true;
	protected $dataStoreMark = "qps"; 
	protected $keyMark = "qpk";
}
