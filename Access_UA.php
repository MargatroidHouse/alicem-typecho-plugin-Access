<?php
if (!defined('__ACCESS_PLUGIN_ROOT__')) {
	throw new Exception('Boostrap file not found');
}

class Access_UA {
	private static $robots = array(
		'360Spider',
		'Alexa (IA Archiver)',
		'Ask',
		'BSpider', // 百度
		'BaiduGame', // 百度游戏
		'BaiduSpider', // 百度
		'Custo',
		'DuckDuckGo-Favicons-Bot', // DuckDuckGo
		'Exabot',
		'Fish search',
		'Google AdSense',
		'Googlebot', // 谷歌
		'Heritrix',
		'Java (Often spam bot)',
		'MJ12bot',
		'MSIECrawler',
		'MSNBot',
		'Netcraft',
		'Nutch',
		'OutfoxBot/YodaoBot',
		'Perl tool',
		'PhantomJS',
		'Qwantify',
		'Sogou Spider', // 搜狗
		'Sogou web spider', // 搜狗WEB
		'Sosospider+',
		'Speedy Spider',
		'StackRambler',
		'SurveyBot',
		'TencentTraveler', // 腾讯TT
		'The web archive (IA Archiver)',
		'TurnitinBot',
		'UptimeRobot', // Uptime在线率检测
		'Voila',
		'WGet tools',
		'Wappalyzer',
		'Yahoo Slurp', // 雅虎
		'Yahoo! Slurp', // 雅虎
		'Yandex bot', // Yandex
		'YandexBot', // Yandex
		'YisouSpider', // 宜搜
		'YoudaoBot', // 有道
		'bingbot', // 必应
		'gce-spider', // 谷歌GCE 
		'ia_archiver',
		'larbin',
		'legs',
		'lwp-trivial',
		'msnbot', // MSN
		'special_archiver',
		'twiceler',
		'yacy',
		//通用
		'Bot',
		'Crawler',
		'Go-http-client', // Go-Http-client 库
		'Spider',
		'Python-urllib', // Python-urllib 库
		'Python-requests', // Python-Requests 库
		'DNSPod-Monitor', // DNSPod D监控
		'Reeder', // RSS 阅读器
		'Tiny Tiny RSS', // RSS 阅读器
		'inoreader', // RSS 阅读器
	);

	private static $robotscn = array(
		'360Spider' => "360",
		'BSpider' => "百度",
		'BaiduGame' => "百度游戏",
		'BaiduSpider' => "百度",
		'DuckDuckGo-Favicons-Bot' => "DuckDuckGo Favicon 收集",
		'Googlebot' => "谷歌",
		'Qwantify' => "Qwant",
		'TurnitinBot' => "Turnitin 图书馆",
		'Yandex bot' => "Yandex",
		'YandexBot' => "Yandex",
		'YisouSpider' => "宜搜",
		'bingbot' => "必应",
	);

	private $ua;
	private $ual;
	
	private $osID	  = null;
	private $osName	= null;
	private $osVersion = null;
	
	private $robotID	  = null;
	private $robotName	= null;
	private $robotVersion = null;
	
	private $browserID	  = null;
	private $browserName	= null;
	private $browserVersion = null;

	function __construct($ua) {
		$this->ua = $ua;
		$this->ual = $this->filter($ua);
	}

	public static function filter($str) {
		return self::removeSpace(strtolower($str));
	}

	protected static function removeSpace($str) {
		return preg_replace('/\s+/', '', $str);
	}

	/**
	 * 获取完整UA信息
	 *
	 * @access public
	 * @return string
	 */
	public function getUA() {
		return $this->ua;
	}

	/**
	 * 获取是否是爬虫
	 *
	 * @access public
	 * @return bool
	 */
	public function isRobot() {
		if ($this->robotID === null) {
			if (!empty($this->ua)) {
				if (preg_match('#([a-zA-Z0-9]+\s*(?:bot|spider))[ /v]*([0-9.]*)#i', $this->ua, $matches)) {
					$this->robotID = $this->robotName = $matches[1];
					$this->robotVersion = $matches[2];
				}
				foreach (self::$robots as $val) {
					if (strpos($this->ual, $this->filter($val)) !== false) {
						$this->robotID = $this->robotName = $val;
						if(preg_match('#' . $this->robotID . '/([a-zA-Z0-9.]+)#i',$this->ua,$submatches))
						{
							$this->robotVersion=$submatches[1];
						}
						break;
					}
				}
			}
			if ($this->robotID		== null) $this->robotID			= '';
			if ($this->robotName	== null) $this->robotName		= '';
			if ($this->robotVersion	== null) $this->robotVersion	= '';
		}
		return $this->robotID !== '';
	}

	/**
	 * 获取爬虫ID
	 *
	 * @access public
	 * @return string
	 */
	public function getRobotID() {
		return $this->isRobot() ? $this->robotID : '';
	}

	/**
	 * 获取爬虫中文名
	 *
	 * @access public
	 * @return string
	 */
	public function getRobotCNName($RobotID){
		return self::$robotscn[$RobotID] ? self::$robotscn[$RobotID] : '';
	}

	/**
	 * 获取爬虫版本
	 *
	 * @access public
	 * @return string
	 */
	public function getRobotVersion() {
		return $this->isRobot() ? $this->robotVersion : '';
	}

	/**
	 * 解析操作系统信息
	 *
	 * @access private
	 * @return bool
	 */
	private function parseOS() {
		if ($this->osID === null) {
			// Windows 系列
			if(preg_match('/Windows NT/i',$this->ua)||preg_match('/WindowsNT/i',$this->ua))
			{
				$this->osID = $this->osName = 'Windows';
				if(preg_match('/10.0/i',$this->ua)||preg_match('/6.4/i',$this->ua))
				{
					$this->osVersion = '10';
				}
				else if(preg_match('/6.3/i',$this->ua))
				{
					$this->osVersion = '8.1';
				}
				else if(preg_match('/6.2/i',$this->ua))
				{
					$this->osVersion = '8';
				}
				else if(preg_match('/6.1/i',$this->ua))
				{
					$this->osVersion = '7';
				}
				else if(preg_match('/6.0/i',$this->ua))
				{
					$this->osVersion = 'Vista';
				}
				else if(preg_match('/5.2/i',$this->ua))
				{
					$this->osVersion = 'Server 2003';
					if(preg_match('/Win64/i',$this->ua))
					{
						$this->osVersion .= '/XP';
					}
				}
				else if(preg_match('/5.1/i',$this->ua))
				{
					$this->osVersion = 'XP';
				}
				else if(preg_match('/5.0/i',$this->ua))
				{
					$this->osVersion = '2000';
				}
				else
				{
					$this->osVersion = '';
				}
				if(preg_match('/WOW64/i',$this->ua)||preg_match('/Win64/i',$this->ua)||preg_match('/X64/i',$this->ua))
				{
					$this->osVersion .= ' 64Bit'; 
				}
			}
			// Linux 系列 
			elseif (preg_match('/Linux/i', $this->ua, $matches)) {
				if (preg_match('/Ubuntu/i', $this->ua, $matches)) {
					$this->osID = $this->osName = 'Ubuntu';
					$this->osVersion = '';
				} elseif (preg_match('/Android ([0-9.]+)/i', $this->ua, $matches)) {
					$this->osID = $this->osName = 'Android';
					$this->osVersion = $matches[1];
				}
				else
				{
					$this->osID = $this->osName = 'Linux';
					$this->osVersion = '';
				}
				if(preg_match('/X86_64/i',$this->ua))
				{
					$this->osVersion .= ' 64Bit'; 
				}
			}
			// Mac OS 系列
			elseif(preg_match('/Mac OS X/i', $this->ua))
			{
				if (preg_match('/iPhone OS ([_0-9]+)/i', $this->ua, $matches)) {
				$this->osID = $this->osName = 'iPhone iOS';
				$this->osVersion = str_replace('_', '.', $matches[1]);
				} elseif (preg_match('/iPad; CPU OS ([_0-9]+)/i', $this->ua, $matches)) {
				$this->osID = $this->osName = 'iPad iOS';
				$this->osVersion = str_replace('_', '.', $matches[1]);
				} elseif (preg_match('/Mac OS X ([0-9_]+)/i', $this->ua, $matches)) {
				$this->osID = $this->osName = 'Mac OS X';
				$this->osVersion = str_replace('_', '.', $matches[1]);
				}
			}
			// Chrome OS 系列
			elseif(preg_match('/CrOS/i',$this->ua))
			{
				if (preg_match('/CrOS i686 ([a-zA-Z0-9.]+)/i', $this->ua, $matches)) {
					$this->osID = $this->osName = 'Chrome OS';
					$this->osVersion = substr($matches[1], 0, 4);
				} elseif (preg_match('/CrOS x86_64 ([a-zA-Z0-9.]+)/i', $this->ua, $matches)) {
					$this->osID = $this->osName = 'Chrome OS';
					$this->osVersion = substr($matches[1], 0, 4) . ' 64Bit';
				}
			}
			else {
				$this->osID = '';
				$this->osName = '';
				$this->osVersion = '';
			}
		}
		return $this->osID !== '' || $this->osName !== '';
	}

	/**
	 * 获取操作系统ID
	 *
	 * @access public
	 * @return string
	 */
	public function getOSID() {
		return $this->parseOS() ? $this->osID : '';
	}

	/**
	 * 获取操作系统名字
	 *
	 * @access public
	 * @return string
	 */
	public function getOSName() {
		return $this->parseOS() ? $this->osName : '';
	}

	/**
	 * 获取操作系统版本号
	 *
	 * @access public
	 * @return string
	 */
	public function getOSVersion() {
		return $this->parseOS() ? $this->osVersion : '';
	}

	/**
	 * 解析浏览器信息
	 *
	 * @access private
	 * @return bool
	 */
	private function parseBrowser() {
		if ($this->browserName === null) {
			if (preg_match('#(Camino|Chimera)[ /]([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = 'Camino';
				$this->browserName = 'Camino';
				$this->browserVersion = $matches[2];
			} elseif (preg_match('#SE 2([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = 'SE 2';
				$this->browserName = '搜狗浏览器 2';
				$this->browserVersion = $matches[1];
			} elseif (preg_match('#360([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = '360';
				$this->browserName = '360浏览器';
				$this->browserVersion = $matches[1];
			} elseif (preg_match('#Maxthon( |\/)([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = $this->browserName = 'Maxthon';
				$this->browserVersion = $matches[2];
			} elseif (preg_match('#XiaoMi/MiuiBrowser/([0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = $this->browserName = '小米浏览器';
				$this->browserVersion = $matches[1];
			} elseif (preg_match('#Safari/([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = $this->browserName = 'Safari';
				$this->browserVersion = $matches[1];
			} 
			// Chrome 内核
			elseif (preg_match('#Chrome/([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				if (preg_match('#XiaoMi/MiuiBrowser/([0-9.]+)#i', $this->ua, $submatches)) {
					$this->browserID = $this->browserName = '小米浏览器';
					$this->browserVersion = $submatches[1];
				}
				else if (preg_match('#Edg/([a-zA-Z0-9.]+)#i', $this->ua, $submatches)) {
					$this->browserID = $this->browserName = 'Microsoft Edge（Chromium内核）';
					$this->browserVersion = $matches[1];
				}
				else {
					$this->browserID = $this->browserName = 'Chrome';
					$this->browserVersion = $matches[1];
				}
			}
			// Opera 系列
			elseif (preg_match('#OPR#i',$this->ua)||preg_match('#Opera',$this->ua))
			{
				if (preg_match('#OPR/([0-9.]+)#i', $this->ua, $submatches)) {
					$this->browserID = $this->browserName = 'Opera';
					$this->browserVersion = $submatches[1];
				} elseif (preg_match('#Opera.([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
					$this->browserID = $this->browserName = 'Opera';
					$this->browserVersion = $matches[1];
				} elseif (preg_match('#opera mini#i', $this->ua)) {
					$this->browserID = $this->browserName = 'Opera Mini';
					$this->browserVersion = $matches[1];
				}
			} elseif (preg_match('#TencentTraveler ([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = 'TencentTraveler';
				$this->browserName = '腾讯TT浏览器';
				$this->browserVersion = $matches[1];
			} elseif (preg_match('#QQ/([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = 'QQ';
				$this->browserName = '手机QQ';
				$this->browserVersion = $matches[1];
			} elseif (preg_match('#UCWEB([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = $this->browserName = 'UCWEB';
				$this->browserVersion = $matches[1];
			} elseif (preg_match('#UCBrowser/([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = 'UCBrowser';
				$this->browserName = 'UC浏览器';
				$this->browserVersion = $matches[1];
			} elseif (preg_match('#Quark/([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = 'Quark';
				$this->browserName = 'Quark浏览器';
				$this->browserVersion = $matches[1];
			} elseif (preg_match('#MSIE ([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = $this->browserName = 'Internet Explorer';
				$this->browserVersion = $matches[1];
			} elseif (preg_match('#Trident#', $this->ua, $matches)) {
				$this->browserID = $this->browserName = 'Internet Explorer';
				$this->browserVersion = '11';
			} elseif (preg_match('#(Firefox|Phoenix|Firebird|BonEcho|GranParadiso|Minefield|Iceweasel)/([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = $this->browserName = 'Firefox';
				$this->browserVersion = $matches[2];
			} elseif (preg_match('#Safari/([a-zA-Z0-9.]+)#i', $this->ua, $matches)) {
				$this->browserID = $this->browserName = 'Safari';
				$this->browserVersion = $matches[1];
			}
			else {
				$this->browserID = '';
				$this->browserName = '';
				$this->browserVersion = '';
			}
		}
		return $this->browserID !== '' || $this->browserName !== '';
	}

	/**
	 * 获取浏览器ID
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowserID() {
		return $this->parseBrowser() ? $this->browserID : '';
	}

	/**
	 * 获取浏览器名字
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowserName() {
		return $this->parseBrowser() ? $this->browserName : '';
	}

	/**
	 * 获取浏览器版本号
	 *
	 * @access public
	 * @return string
	 */
	public function getBrowserVersion() {
		return $this->parseBrowser() ? $this->browserVersion : '';
	}
}
