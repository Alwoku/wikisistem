<!-- Вывод уведомлений -->
<toastr
    :messages='@json(session("messages"))'
    :errors='@json(session("errors"))'
></toastr>
