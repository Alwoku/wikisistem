<footer class="footer">
    <div class="columns">
        <div class="column is-4">
            Система хранения знаний
            <br>
            © Айсвуд 2015–{{ date("Y") }}
            <br>
            <div class="note">
                ver. {{ config("basis.version.version") }} от {{ $version->format("d.m.Y") }}
                ({{ $version->diffForHumans() }}),
                Laravel {{ $laravelVersion }}, PHP {{ phpversion() }}
                <br>
                Информация на странице носит конфиденциальный характер
            </div>
        </div>
    </div>
</footer>

