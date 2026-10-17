<?php

class Def
{
  //  ，类的构造函数
  function  __construct()
  {
    var_dump(__METHOD__);
  }
  // ，类的析构函数
  function __destruct()
  {
    var_dump(__METHOD__);
  }
  // ，在对象中调用一个不可访问方法时调用
  function __call($name, $arguments)
  {
    var_dump(__METHOD__);
  }
  // ，用静态方式中调用一个不可访问方法时调用
  public static function __callStatic($name, $arguments)
  {
    var_dump(__METHOD__);
  }
  // ，获得一个类的成员变量时调用
  function __get($name)
  {
    var_dump(__METHOD__);
  }
  // ，设置一个类的成员变量时调用
  function __set($name, $value)
  {
    var_dump(__METHOD__);
  }
  // ，当对不可访问属性调用isset()或empty()时调用
  function __isset($name)
  {
    var_dump(__METHOD__);
  }
  // ，当对不可访问属性调用unset()时被调用。
  function __unset($name)
  {
    var_dump(__METHOD__);
  }
  // ，执行serialize()时，先会调用这个函数
  function __sleep()
  {
    var_dump(__METHOD__);
  }
  // ，执行unserialize()时，先会调用这个函数
  function __wakeup()
  {
    var_dump(__METHOD__);
  }

  // ，类被当成字符串时的回应方法
  function __toString()
  {
    var_dump(__METHOD__);
  }
  // ，调用函数的方式调用一个对象时的回应方法
  function __invoke()
  {
    var_dump(__METHOD__);
  }
  // ，调用var_export()导出类时，此静态方法会被调用。
  function __set_state()
  {
    var_dump(__METHOD__);
  }
  // ，当对象复制完成时调用
  function __clone()
  {
    var_dump(__METHOD__);
  }
  // ，尝试加载未定义的类
  function __autoload()
  {
    var_dump(__METHOD__);
  }
  // ，打印所需调试信息
  function __debugInfo()
  {
    var_dump(__METHOD__);
  }
}
