/* Мой хороший календарь,
 * с шахматами и поэтессами
 */

function Kalender(input, parent, toggle_button) {
    var me = this; // магия

    /* Перерисовывает
     */
    this.repaint = function() {
        var days = ['пн', 'вт', 'ср', 'чт', 'пт', 'сб', 'вс'];
        var mouths = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
        'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

        me.my_div.innerHTML = ''; // вместо removeAllChilds()

        var table = me.create('table', me.my_div);

        // Первая строчка (месяц, год и кнопки)
        var th = me.create('th', me.create('tr', table));
        th.colSpan = 7;

        var span = me.create('span', th);
        span.innerHTML = '←';
        span.onclick = me.mouthBack;
        span.className = 'ch_month back';

        me.createTN(' ', th);

        span = me.create('span', th);
        span.innerHTML = mouths[me.cur_mouth.getMonth()] + ' ' + me.cur_mouth.getFullYear();
        span.className = 'month_name';

        me.createTN(' ', th);

        span = me.create('span', th);
        span.innerHTML = '→';
        span.onclick = me.mouthForward;
        span.className = 'ch_month forward';

        // Вторая строчка (дни недели)
        var tr = me.create('tr', table);
        tr.className = 'day_names'
        for (var i = 0; i < 7; i++){
            var td = me.create('td', tr);
            td.innerHTML = days[i];
        }

        // Остальные строчки (числа месяца)
        me.cur_mouth.setDate(1);
        var d_count = (new Date(me.cur_mouth.getFullYear(), me.cur_mouth.getMonth() + 1, 0)).getDate();

        var day = me.cur_mouth.getDay() - 1;

        tr = me.create('tr', table);
        tr.className = 'regular';
        for (var i = 0; i < day; i++)
            me.create('td', tr).className = 'empty';

        for (var i = 0; i < d_count; i++) {
            if (day == 7) {
                day = 0;
                tr = me.create('tr', table);
            }
            day++;
            var td = me.create('td', tr);

            td.onclick = function() {
                me.hide();
                var tmp = me.cur_date;
                tmp.setYear( me.cur_mouth.getFullYear() );
                tmp.setMonth( me.cur_mouth.getMonth() );
                tmp.setDate( this.innerHTML );
                me.setCurDate(tmp);
                me.writeDateToInput();
            };
            td.innerHTML = i + 1;
        }

        for (;day < 7; day++)
            me.create('td', tr).className = 'empty';
    };

    

    /* Сдвигает календарь на месяц назад
     */
    this.mouthBack = function() {
        me.cur_mouth.setMonth(me.cur_mouth.getMonth() - 1);
        me.repaint();
    };

    /* Сдвигает календарь на месяц вперед
     */
    this.mouthForward = function() {
        me.cur_mouth.setMonth(me.cur_mouth.getMonth() + 1);
        me.repaint();
    };

    /* Устанавливает текущюю дату, и обнуляет сдвиг календаря.
     */
    this.setCurDate = function ( date ) {
        me.cur_date = date;
        me.cur_mouth = new Date(date);
    };
    


    /* * * * * * * * * * * * * * * * * * * * * *\
    |                 Отображение               |
    \* * * * * * * * * * * * * * * * * * * * * */

    /* Отображение
     */
    this.show = function() {
        me.my_div.style.display = 'block';
        me.readAndRepaint();
        me.input.onkeyup = me.checkDatePlus;
        me.hidden = false;
    };
    /* Скрытие
     */
    this.hide = function() {
        me.my_div.style.display = 'none';
        me.input.onkeyup = me.checkDate;
        me.hidden = true;
    };
    /* Переключение
     */
    this.toggle = function() {
        me.hidden ? me.show() : me.hide();
    };




    /* * * * * * * * * * * * * * * * * * * * * *\
    |             Работа с input'ом             |
    \* * * * * * * * * * * * * * * * * * * * * */

    /* Считывание
     */
    this.readDateFromInput = function() {
        if(me.isCorrectInput())
            me.setCurDate( new Date( me.input.value ) );
        else {
            alert('Не верный формат!\nДата установлена на сегодня');
            me.setCurDate( new Date() );
        }
    };
    /* Запись
     */
    this.writeDateToInput = function() {
        var cd = me.cur_date;
        me.input.value = cd.getFullYear() + '-' + (cd.getMonth()+1) + '-' + cd.getDate() + ' ' + cd.toLocaleTimeString();
        me.checkDate();
    }
    /* Проверяет корректность даты в инпуте,
     * и делает текст красным если дата не корректна
     */
    this.checkDate = function() {
        me.input.style.color = me.isCorrectInput() ? me.defaultInputColor : 'red';
    };

    /* Проверяет дату в инпуте на корректность
     */
    this.isCorrectInput = function() {
        return (new Date( me.input.value )).getTime();
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
    this.create = function(type, parent) {
        var elem = document.createElement(type);
        if (parent)
            parent.appendChild(elem);
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
    |      Обертки        |
    \* * * * * * * * * * */

    /* checkDate + Обновление календаря
     */
    this.checkDatePlus = function() {
        me.checkDate();
        if (me.isCorrectInput())
            me.readAndRepaint();
    };

    /* Считывает и перерисовывает
     */
    this.readAndRepaint = function() {
        me.readDateFromInput();
        me.repaint();
    };




    /* * * * * * * * * * *\
    |     Конструктор     |
    \* * * * * * * * * * */

    this.input = this.byId(input);
    this.parent = this.byId(parent);
    this.toggle_button = this.byId(toggle_button);

    this.hidden = true;

    this.my_div = this.create('div', this.parent);
    this.my_div.style.display = 'none';
    this.my_div.className = 'Kalender';

    this.defaultInputColor = this.input.style.color;
    this.input.onkeyup = this.checkDate;

    this.toggle_button.onclick = this.toggle;
}