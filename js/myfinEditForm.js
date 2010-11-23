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
        me.draw_value_block();

        me.create('input', me.create('div', me.form,
        {
            className: 'input_block'
        }), {
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

        me.create('div', input_block, {
            className: 'note',
            innerHTML: 'любой текст, не более 300 символов'
        });
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

        me.create('div', input_block, {
            className: 'note',
            innerHTML: 'через запятую, пример: <i>зарплата, основная работа, на карточку</i>'
        });

        me.tag_list_div = me.create('div', input_block, {
            className: 'tag_list'
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

    this.draw_value_block = function() {
        var input_block = me.create('div', me.form,
        {
            className: 'input_block'
        });

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

        me.create('div', input_block, {
            className: 'note',
            innerHTML: 'примеры: <i>100.42, 100,42, 5100, 5 100, 9 999.99</i>'
        });

        me.value_input.onfocus = function() {
            if(this.value=='0')
                this.value='';
        };

        me.value_input.onblur = function() {
            if(this.value=='')
                this.value='0';
        };
        
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

    // тут тоже лажа (надо не document а body)
    this.parent = params.parent ? this.byId(params.parent) : document;
}