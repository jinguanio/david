window.onload = function() {
    var area = document.getElementById('text');

    area.rows = 10;
    area.cols = 50;
    area.style.margin = '10px';
    area.style.border = '1px solid red';

    area.value = 'As client-side JavaScript programming has evolved, so has the event model it supports. With each new browser version, new event-handler attributes have been added. Finally, the HTML 4 specification codified a standard set of event handler attributes for HTML tags. The third column of Table 17-1 specifies which HTML elements support each event handler attribute. For mouse event handlers, column three specifies that the handler attribute is supported by "most elements." The HTML elements that do not support these event handlers are typically elements that belong in the <head> of a document or do not have a graphical representation of their own. The tags that do not support the nearly universal mouse event handler attributes are <applet>, <bdo>, <br>, <font>, <frame>, <frameset>, <head>, <html>, <iframe>, <isindex>, <meta>, and <style>.';
    area.onmouseup = function(evt) {
        alert(getSelectText2(area));
    }

    function getSelectText2(t) {
        if(window.getSelection) { // ff/chrome
            if(t.selectionStart != undefined && t.selectionEnd != undefined) {
                return t.value.substring(t.selectionStart, t.selectionEnd);
            } else {
                return "";
            }
        } else { // ie
            return document.selection.createRange().text;
        }
    }
}

