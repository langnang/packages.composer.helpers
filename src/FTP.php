<?php

class FTP
{
  function __construct($host, $username, $password)
  {
    $this->connection = ftp_connect($host);
    ftp_login($this->connection, $username, $password);
  }
}
