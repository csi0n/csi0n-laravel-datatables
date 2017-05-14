### Laravel Request

####简介

>简单的分离Laravel请求规则

####实例
#####表单
```
<form action="{{route('test.update',['id'=>1])}}" method="post">
    {{csrf_field()}}
    @crulemethod("other_method")
    First name:<br>
    <input type="text" name="first" value="c">
    <br>
    Last name:<br>
    <input type="text" name="last" value="si0n">
    <br><br>
    <input type="submit" value="Submit">
</form>
```
#####Request中
```
   function setCRule()
    {
        CRule::COMMON([
            'first' => 'required'
        ]);
        CRule::CUSTOM('other_method',[
            'last' => 'required'
        ]);
//        CRule::POST([
//            'first' => 'required',
//            'last' => 'required'
//        ]);
//        CRule::PATCH([
//            'last' => 'required'
//        ]);
    }
```
####使用方法

app.php->providers中添加:
```
csi0n\Laravel\Request\Providers\CLaravelRequestServiceProvider::class
```

app.php->aliases中添加:
```
'CRule' => csi0n\Laravel\Request\Facades\CLaravelRequestFacade::class
```

使用的Request需要继承
```
csi0n\Laravel\Request\Requests\CLaravelRequest
```





