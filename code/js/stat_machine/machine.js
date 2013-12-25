(function(window) {
    var Stat = {
            HIDE: 1,
            SHOW: 2,
        };

    var To = {
        s: Stat.SHOW,
        h: Stat:HIDE
    };

    var currStat = Stat.HIDE,

    var get = function(id) {
            return document.getElementById(id);
        },

    var bindEvent = function(id, func) {
        var evtType = 'click';

        if (document.all) {
            get(id).attachEvent('on'+evtType, func);
        } else {
            get(id).addEventListener(evtType, func, false);
        }
    };

    var Menu = {
        show: function(id) {
            this.g(id).style.display = '';
        },

        hide: function(dom) {
            this.g(id).style.display = 'none';
        },

        g: get,

        bind: function(cfg) {
            var fsm = {}, pare, chil;
            var x, y, i;

            if (cfg.events) {
                for (x in cfg.events) {
                    i = cfg.events[x];
                    fsm[i.name] = function(id) {
                        currStat = To[i.to];
                        this[i.name](id);
                    }
                }
            }

            if (cfg.init) {
                for (x in cfg.init) {
                    pare = cfg.init[x].parent;
                    chil = cfg.init[x].child;
                }
            }

            return fsm;
        },
    };

    window.Menu = Menu;
})(this)
