<div class="sorting_panel">
    <form id="sort_form" action="{{ url()->current() }}" method="GET">
        <label for="sort_select">Показать:</label>
        <select onchange="sort_form.submit()" name="sort" id="sort_select">
            <option @selected(request('sort')==="Новые") value="Новые">Новые</option>
            <option @selected(request('sort')==="Сначала дешевые") value="Сначала дешевые">Сначала дешевые</option>
            <option @selected(request('sort')==="Сначала дорогие")  value="Сначала дорогие">Сначала дорогие</option>
        </select>
    </form>
</div>
