<template>
    <div>

        <p class="menu-label">Настройки</p>

        <ul class="menu-list">
            <li>
                <router-link to="/settings/users">
                    Пользователи
                </router-link>
            </li>
            <li>
                <router-link to="/settings/groups">
                    Группы
                </router-link>
            </li>
            <li>
                <router-link to="/settings/expansion-files">
                    Расширения загружаемых файлов
                </router-link>
            </li>
            <li>
                <router-link to="/settings/deleted-objects">
                    Удаленные объекты
                </router-link>
            </li>
            <li>
                <router-link to="/settings/suggested-changes">
                    Предложения об изменении
                </router-link>
            </li>
            <li>
                <router-link to="/settings/history-of-changes">
                    История изменений сайта
                </router-link>
            </li>

            <li>
                <router-link to="/settings/feedback">
                    Обратная связь
                </router-link>
            </li>
        </ul>
        
    </div>
</template>
<script>
export default {
    
}
</script>