# widgets

- Tool 小工具函数集合
- curl  curl类
- FileCache 文件缓存类
- Validator 验证数据的格式是否符合要求
- SysInfo 获取Linux基本硬件信息
- FIleManger 管理操作系统文件

##### 更多使用方法详见文件注释

## curl
修改自 php-mod/curl https://github.com/php-mod/curl
post方式增加了queryString
无报错情况下直接返回获得的数据
```
//实例化
$curl = new Curl\Curl();
$curl->get('http://www.example.com/');
$curl->get('http://www.example.com/search', array('queryString'=>'val'));
$curl->post('http://www.example.com/login/', array('username' => 'myusername','password' => 'mypassword'));
$curl->post('http://www.example.com/login/', array('username' => 'myusername','password' => 'mypassword'),array('queryString'=>'val'));
$curl->setCookie('key', 'value');

$curl->setBasicAuthentication('username', 'password');
$curl->setUserAgent('');
$curl->setReferrer('');
$curl->setHeader('X-Requested-With', 'XMLHttpRequest');

//异常会返回false,获得错误信息
$res=$curl->get('http://www.example.com/');
if (!$res) {
echo $curl->error_code;
echo $curl->error_message;
}

//自定义curl参数
$curl->setopt(CURLOPT_SSL_VERIFYPEER, FALSE);

//获得header信息
var_dump($curl->request_headers);
var_dump($curl->response_headers);

$curl->put('http://api.example.com/user/', array(
'first_name' => 'Zach',
'last_name' => 'Borboa',
));
$curl->patch('http://api.example.com/profile/', array(
'image' => '@path/to/file.jpg',
));
$curl->delete('http://api.example.com/user/', array(
'id' => '1234',
));

//对象销毁时会自动调用
$curl->close();
```

## Tool
`Tool::getPrivateValue`: 动态的获得实例的私有属性值
```
    /**
     * 用法:
$value=Tool::getPrivateValue('property',$obj)
var_dump($value);
     *
     * @param string $fieldName 要获取的属性名。
     * @param object $obj 从哪个对象里获取
     * @return mixed 对象的私有属性值。
     */
```
`Tool::responseNow($outputString)`
-  !!!重要，此功能对部分httpServer无效，比如IIS和其他小的build-in webServer.Apache2.4.X测试有效
- 将输出传入的数据，并断开与客户端的连接，然后再执行后面的语句。这样即使后面的语句有输出也将无法显示给客户端
- 可以用作先输出再做记录日志等耗时操作。

例子：
```
$data='立马就输出到浏览器啦';
\glacier\widgets\Tool::responseNow($data);
//下面语句再输出到浏览器后继续执行
sleep(30);
//记录日志
file_put_contents('1.txt','test');
```
`proc_exec`:Linux下可以获得程序错误信息的方法

## FileCache
文件缓存类，利用文件系统作为缓存
 * 原作者Author: Jenner
 * 基于http://www.huyanping.cn/php%E6%96%87%E4%BB%B6%E7%BC%93%E5%AD%98%E5%AE%9E%E7%8E%B0/ 上文档修改

 实例化的时候应传入一个缓存路径，否则默认把缓存放在这个类文件所在的目录
 ```
     /**
      * 功能实现：get、set、has、increment、decrement、delete、flush(删除所有)
          *为了避免一个文件内的数据过大，造成读取文件的时候延迟较高，我们采用一个key-value一个文件的方式实现存储结构。
      *为了支持key过期，我们需要把expire数据写入到文件中，所以需要对写入的数据进行序列化处理
      *为了能够快速的定位到文件路径，我们采用hash算法一次计算出文件位置
      * 避免过多缓存文件放在同一文件夹下，所以 参看path()函数
      * 根据md5($key)实现3级目录存放文件 例如： /home/b0/68/b068931cc450442b63f5b3d276ea4297
      * 缓存目录
      * @var
      **/
    //用法：
    new FileCache(缓存根目录名)
     get($key) //获取值
     set($key,$val,[$过期时间，不设置表示不过期]) //单位是秒
     increment($key,num) //表示值+1或者自定义

 ```
## Validator
`validate( $array,$filterRules)`

参数：
* @param array $filterRules 要验证的规则可多选用`|`分隔
* @param array $array  要验证的数组（）
格式如下
* $filterRules="required|max:6";
* $var=123456;
* 新增验证规则新增一个函数即可
 
返回值：通过`true` 失败`false`

例子：
```
$age=40;
$filterRules='between:18,60';
$obj->validate($age,$filterRules);

//  $filterRules=["varName"=>"required|max:6"];
//*$array=["varName"=>"水水水水水水水水"];通常是$_POST 这个数组 
$filterRules=["username"=>"required|max:6",
'password'=>'required|min:6',
'mobileNumber'='required|phone'];
$obj->validateArray( $_POST,$filterRules)

```