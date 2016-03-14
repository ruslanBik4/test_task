<?php
/*
* Напишете пакет для Composer, который будет заниматься тем, что с удаленного хоста загружать картинки и сохранять их на ФС.
*/

/**
*  Это определение класса,предоставляющего доступ к удаленному хосту ftp
*/
class ftp_Wanderer  {

    private $conn_id;
    private $is_login;
    
    protected $host;
    protected $port;
    protected $timeout;
    protected $login;
    protected $password;
    
    public function __construct($host, $port = 21, $timeout = 90)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }
    public function __destruct()
    {
        if( isset($this->conn_id) )
            ftp_close($conn_id);
    }
    //устанавливаем соединение
    protected function Opened()
    {
        $this->conn_id = ftp_connect($this->host, $this->port, $this->timeout);
        if ($this->conn_id === false)
            throw Exception("Не удалось установить соединение с ".$this->host);
            
        return $this->conn_id;
    }
    // получаем соединение
    final public function GetConnection()
    {
        if( isset($this->conn_id) )
            return $this->conn_id;
        else
            return $this->Opened();
    }
    // работа с параметрами подключения
    public function Set_option($option, $value)
    {
        return ftp_set_option($this->GetConnection(), $option, $value);
    }
    public function Get_option($option)
    {
        return ftp_get_option($this->GetConnection(), $option);
    }
    // подключение к серверу
    protected function Login($login, $password)
    {
        
        if ($this->is_login)
           if ( ($this->login == $login) && ($this->password == $password) )
               return true;
        
        $this->login == $login;
        $this->password == $password;
        
        
        return ($this->is_login = ftp_login($this->GetConnection(), $this->login, $this->password));
    }

}



/**
* Class для перекачки картинок с удаленного сервера на свой ФС
*/
class ftp_ImagesDownloader extends ftp_Wanderer {

    const img_types = 'jpg;png;gif';
    
    private $path;
    private $mask;
    private $tmpfileName;
    
    public function __construct($host, $port = 21, $timeout = 90)
    {
        parent::__construct($host, $port, $timeout);
    }
    public function __destruct()
    {
        if(isset($this->tmpfileName))
            unlink($this->tmpfileName);
            
        parent::__destruct();
    }
    // создаем временный файл
    private function CreateTmpFile()
    {
       if( ($this->tmpfileName = tempnam(sys_get_temp_dir(), "tmp")) === false)
           throw Exception("Не удалось создать временный файл для приема данных."); 
           
       return $this->tmpfileName; 
    }
    private function GetTmpFileName()
    {
        return $this->tmpfileName;
    }
    private function GetImageFromURL($url)
    {
        file_put_contents($this->GetTmpFileName(), file_get_contents($url));
    }
    private function GetImageFromPHP($path)
    {
        $ch = curl_init($path);
        $fp = fopen( $this->GetTmpFileName(), 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }
	public function Download($login, $password, $path, $mask)
	{
        if ( !($this->login($login, $password)) )
            throw Exception("Не удалось подключиться к {$this->host} с логином $login");
            
        CreateTmpFile();
        if ( preg_match( '/\.php$/', $path) )
            GetImageFromPHP($path);
        elseif( preg_match('/\.[' + $this::img_types + ']$/', $path) )
        	GetImageFromURL($path);
        else
            throw Exception("Непонятный формат данных.");	
            
        return ( ftp_put($this->GetConnection(), '', $this->GetTmpFileName(), FTP_BINARY) );	
	}

}

