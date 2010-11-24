/* Welcome to spaghetti code, enjoy!
 **/

function myfinEditForm(params) {

    var me = this;

    this.draw = function() {

        me.form = me.create('form', me.parent,
        {
            action: me.action,
            method: me.method
        });

        me.draw_description_input();
        me.draw_tags_input();

        var tr = me.create('tr', me.create('table', me.form, {
            className: 'value_date_tbl'
        }));
        me.draw_value_block(me.create('td', tr, {
            width: '280'
        }));
        me.draw_date_input(me.create('td', tr));
        me.create('input', me.create('td', tr, {
            vAlign: 'bottom',
            align: 'right'
        }), {
            className: 'save_button',
            type: 'submit',
            value: 'Сохранить'
        });     

        me.create('input', me.form, {
            type: 'hidden',
            name: 'id',
            value: me.id
        });
    };

    // Поле описание
    this.draw_description_input = function(){
        var input_block = me.create('div', me.form,
        {
            className: 'input_block'
        });

        var label = me.create('label', input_block, {
            innerHTML: 'Описание<br>'
        });

        me.description_input = me.create('input', label,
        {
            name: 'description',
            type: 'text',
            value: me.description,
            className: 'description_input'
        });

        me.description_input.onfocus = function() {
            if(this.value=='нет описания ...')
                this.value='';
        };

        me.description_input.onblur = function() {
            if(this.value=='')
                this.value='нет описания ...';
        };
    };

    // Поле теги
    this.draw_tags_input = function(){
        var input_block = me.create('div', me.form,
        {
            className: 'input_block'
        });

        var label = me.create('label', input_block, {
            innerHTML: 'Теги<br>'
        });

        me.tags_input =  me.create('input', label,
        {
            name: 'tags',
            type: 'text',
            value: me.tags,
            className: 'tags_input'
        });

        me.tag_list_div = me.create('div', input_block, {
            className: 'form_tag_list'
        });

        for (i = 0; i < me.tag_list.length; i++) {
            me.create('span', me.tag_list_div, {
                innerHTML: me.tag_list[i],
                onclick: function() {
                    me.tags_input.value += ((me.tags_input.value == '') ? '' : ', ') + this.innerHTML;
                }
            });

            me.createTN(" ", me.tag_list_div);
        }
    };

    // Рисует поле ссумы (с радио)
    this.draw_value_block = function(input_block) {
        var label = me.create('label', input_block, {
            innerHTML: 'Сумма<br>'
        });

        me.value_input =  me.create('input', label,
        {
            name: 'value',
            type: 'text',
            value: me.value,
            className: 'value_input'
        });

        me.createTN(" ", input_block);

        label = me.create('label', input_block);
        me.type_radio_in = me.create('input', label, {
            type: 'radio',
            name: 'type',
            value: 1
        });
        me.createTN(" ", label);
        me.create('span', label, {
            className: 'money_in',
            innerHTML: 'прибыль'
        });

        me.createTN(" ", input_block);

        label = me.create('label', input_block);
        me.type_radio_out = me.create('input', label, {
            type: 'radio',
            name: 'type',
            value: 0
        });
        me.createTN(" ", label);
        me.create('span', label, {
            className: 'money_out',
            innerHTML: 'расход'
        });

        (me.type ? me.type_radio_in : me.type_radio_out).checked = true;

        me.value_input.onfocus = function() {
            if(this.value=='0')
                this.value='';
        };

        me.value_input.onblur = function() {
            if(this.value=='')
                this.value='0';
        };
        
    };

    this.draw_date_input = function(input_block) {
        var input_id = 'in' + me.randomString();

        me.create('label', input_block, {
            innerHTML: 'Дата<br>',
            htmlFor: input_id
        });

        var cal_wrap = me.create('div', input_block, {
            className: 'cal_wrap'
        });

        me.date_input = me.create('input', cal_wrap,
        {
            name: 'date',
            type: 'text',
            value: me.date,
            className: 'date_input',
            id: input_id
        });

        me.toggle_cal = me.create('div', cal_wrap, {
            className: "toggle_cal"
        });
        me.calendar = me.create('div', cal_wrap, {
            className: "calendar"
        });

        me.kalender = new Kalender(me.date_input, me.calendar, me.toggle_cal);

        if (me.date_input.value == 0)
            me.kalender.writeDateToInput();
    };

    this.show = function() {
        me.hidden = false;
        if (!me.form)
            me.draw();

        me.form.style.display = 'block';
    };

    this.hide = function() {
        me.hidden = true;
        if (!me.form)
            return;

        me.form.style.display = 'none';
    };

    this.toggle = function() {
        me.hidden ? me.show() : me.hide();
    };


    /* * * * * * * * * * *\
    |     Служебные       |
    \* * * * * * * * * * */

    /* Возвращает элемент по id
     */
    this.byId = function (node) {
        return typeof node == 'string' ? document.getElementById(node) : node
    };

    /* Создает элемент и добавляет его в parent
    */
    this.create = function(type, parent, params) {
        var elem = document.createElement(type);
        if (parent)
            parent.appendChild(elem);

        if (params){
            for(var prop in params) {
                if (!params.hasOwnProperty(prop)) continue
                elem[prop] = params[prop]
            }
        }

        return elem;
    };

    this.randomString = function () {
        return (new Date().getTime()).toString(16) + '_' + (Math.floor(Math.random() * 256)).toString(16);
    };

    /* Создает TextNode и добавляет его в parent
    */
    this.createTN = function(text, parent) {
        var elem = document.createTextNode(text);
        if (parent)
            parent.appendChild(elem);
        return elem;
    };



    /* * * * * * * * * * *\
    |     Конструктор     |
    \* * * * * * * * * * */

    if (params === undefined)
        params = {};

    this.description = params.description || 'нет описания ...';
    this.tags = params['tags'] || '';
    this.value = params['value'] || 0;
    this.type = params.type || 0;
    this.date = params.date || 0; //тут лажа, но пока это не нужно (надо не 0 а строку )
    this.id = params.id || 0;

    this.tag_list = params.tag_list || [];

    this.action = params.action || '';
    this.method = params.method || 'post';

    this.toggle_button = params.toggle_button ? this.byId(params.toggle_button) : null;

    // тут тоже лажа (надо не document а body)
    this.parent = params.parent ? this.byId(params.parent) : document;

    this.hidden = true; 

    if (this.toggle_button) 
        this.toggle_button.onclick = me.toggle;
}

/* Прошу прощения у всех кому пришлось работать с этим кодом.
 **/