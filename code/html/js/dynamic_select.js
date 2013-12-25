window.onload = function() {
    var _Cont, _Sel, _Opt = [], _opt;
    var i;
    var gap = ' ';

    String.prototype.repeat = function(num) {
        var s = '';
        for (var i = 0; i < num; i++) {
            s += this.toString();
        }
        return s;
    };

    _Cont = document.getElementById('cont');

    _Sel = document.createElement('select');
    _Sel.name = 'cate';
    _Sel.id = 'cate';

    _opt = new Option('a', 'a');
    _Opt.push(_opt);

    _opt = new Option(gap.repeat(2)+'b', 'b');
    _Opt.push(_opt);

    _opt = new Option(gap.repeat(4)+'c', 'c');
    _Opt.push(_opt);

    _opt = new Option('d', 'd');
    _Opt.push(_opt);

    _opt = new Option(gap.repeat(2)+'e', 'e');
    _Opt.push(_opt);

    for (i in _Opt) {
        _Sel.add(_Opt[i]);
    }

    _Cont.appendChild(_Sel);
}
