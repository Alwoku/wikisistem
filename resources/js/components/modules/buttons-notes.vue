<!-- панель кнопок статьи/папки -->
<template>
    <div>
        <div class="buttons has-addons">
            <button class="button"
                title="История">
                <span class="material-icons">
                    schedule
                </span>
                <span v-if="!$isMobile()">
                    История
                </span>
            </button>
            <button class="button"
                title="предложить изменения">
                <span class="material-icons">
                    history_edu
                </span>
                <span v-if="!$isMobile()">
                    Предложить изменения 
                </span>
            </button>
            <button class="button">
                <span class="material-icons">
                    edit
                </span>

                <span v-if="!$isMobile()">
                    Редактировать
                </span>
            </button>
        </div>
    </div>
</template>
<script>
export default {
    
}
</script>