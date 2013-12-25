// ref: http://www.ruanyifeng.com/blog/2012/07/three_ways_to_define_a_javascript_class.html
var FrameType = 'Simple';

switch (FrameType) {
case 'Niming':
    // {{{

    // 利用 (function(){ ... })() 方式
    // 建立程序框架
    var test = (function() {
        var test = this;
        test.no = function() {
            alert('i am notify in [Niming]');
        };

        return test;
    })();

    test.no2 = function() {
        alert('i am no2 in [Niming]');
    };

    test.no();
    test.no2();

    // }}}
    break;

case 'Niming2':
    // {{{

    // 利用 (function(){ ... })() 方式
    // 建立程序框架
    var test;

    if (!Object.create) {
        Object.create = function (o) {
            function F() {}
            F.prototype = o;
            return new F();
        };
    }

    test = Object.create(null);
    test.no = function() {
        alert('i am notify in [Niming2]');
    };
    test.no2 = function() {
        alert('i am no2 in [Niming2]');
    };

    test.no();
    test.no2();

    // }}}
    break;

case 'New':
    // {{{
    
    // 利用构造函数形式
    // 建立程序框架
    var test = new function() {
        var test = this;
        test.no = function() {
            alert('i am notify in [New]');
        };
    };

    test.no2 = function() {
        alert('i am no2 in [New]');
    };

    test.no();
    test.no2();

    // }}}
    break;

case 'Simple':
    // {{{
    
    // 简单方式创建 js 对象
    var Obj = {
        createNew: function(){
            var obj = {};

            obj.no = function() {
                alert('i am no2 in [Simple]');
            };

            return obj;
        }
    };

    var test = Obj.createNew();
    test.no2 = function() {
        alert('i am no2 in [Simple]');
    };

    test.no();
    test.no2();

    // }}}
    break;
}
