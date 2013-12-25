// 可以放到 firefox firebug 执行看效果
// 模拟类的私有方法不允许继承的方法
// 如 php private function
function C() {
    var that = this;
    this.f   = function() { f2(); };
    var f2   = function() { that.f4(); };
    this.f4  = function() { console.info(2); };
}

function C2() {
    C.apply(this);
    this.f3 = function() { this.f(); };
}

var o = new C2;
o.f3();
