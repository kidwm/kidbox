# 非成員導向 #
在模板裡面可以這樣使用：
```
<?php
if (!member_check())
header('location: '.OUT_PATH);
?>
```
上面的範例是導向首頁。
加在header.tpl.php的最前面的話，(當然首頁就不能引入header.tpl.php)
就可以做出鎖國效果了。
另外也可以用在index.inc.php上。
```
<?php 
if (member_check())
header('location: '.OUT_PATH.'main');
?>
```
在首頁登入後，指定要轉向到哪裡。