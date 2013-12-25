var ExtendType = 'jquery';

function Animal(){
    this.species = "动物";
}

function Cat(name,color){
    this.name = name;
    this.color = color;
}

switch (ExtendType) {
case 'apply':
    function Cat(name,color){
        Animal.apply(this, arguments);
        this.name = name;
        this.color = color;
    }

    var cat1 = new Cat("大毛","黄色");
    alert('apply: ' + cat1.species); // 动物
    break;

case 'prototype':
    Cat.prototype = new Animal();
    Cat.prototype.constructor = Cat;
    var cat1 = new Cat("大毛","黄色");
    alert('prototype: ' + cat1.species); // 动物

    //alert(Cat.prototype.constructor == Animal); // true
    //alert(cat1.constructor == Cat.prototype.constructor); // true
    //alert(cat1.constructor == Animal); // true
    break;

case 'yui':
    function extend(Child, Parent) {
        var F = function(){};
        F.prototype = Parent.prototype;
        Child.prototype = new F();
        Child.prototype.constructor = Child;
        Child.uber = Parent.prototype;
    }

    function Animal(){ }
    Animal.prototype.species = "动物";
    Animal.prototype.func = function() { alert('yui: I am parent function.'); };


    extend(Cat, Animal);
    var cat1 = new Cat("大毛","黄色");
    alert('yui: ' + cat1.species); // 动物
    Animal.prototype.func();
    break;

case 'jquery': // 非构造函数的继承
    function deepCopy(p, c) {
        var c = c || {};

        for (var i in p) {
            if (typeof p[i] === 'object') {
                c[i] = (p[i].constructor === Array) ? [] : {};
                deepCopy(p[i], c[i]);
            } else {
                c[i] = p[i];
            }
        }

        return c;
    }

    var Chinese = { nation:'中国' };
    Chinese.birthPlaces = ['北京','上海','香港'];
    var Doctor = { career:'医生' };
    Doctor = deepCopy(Chinese, Doctor);
    Doctor.birthPlaces.push('厦门');
    alert(Doctor.birthPlaces); //北京, 上海, 香港, 厦门
    alert(Chinese.birthPlaces); //北京, 上海, 香港
    break;

case 'simple':
    var Animal = {
        createNew: function(){
           var animal = {};
           animal.sleep = function(){ alert("睡懒觉"); };
           return animal;
       }
    };

    var Cat = {
        createNew: function(){
           var cat = Animal.createNew();
           var sound = '喵喵喵';
           cat.name = "大毛";
           cat.makeSound = function(){ alert("喵喵喵"); };
           return cat;
        }
    };

    var cat1 = Cat.createNew();
    cat1.sleep(); // 睡懒觉
    cat1.makeSound(); // 喵喵喵
    break;
}
