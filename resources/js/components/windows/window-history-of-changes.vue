<template>
    <div class="modal is-active">
        <div class="modal-background"  @click="$emit('close-modal')"></div>
        <div class="modal-card">

            <header class="modal-card-head">
                <p class="modal-card-title">
                    Информация об изменениях
                    <br>
                    <span class="has-text-danger is-size-6" v-if="footerData.reload">
                        Внимание!
                        <br> 
                        Версия сайта изменилась. 
                        Пожалуйста, обновите страницу.
                    </span>
                </p>
                <p>Версия: {{ footerData.versionSite }}</p>
            </header>

            <section class="modal-card-body">
                <div class="field" v-if="user.is_admin && footerData.info.admin_info">
                    <label class="label">
                        Для админов
                    </label>

                    <md-editor-component
                        :text="footerData.info.admin_info"
                        :edit="false"
                    ></md-editor-component>
                </div>
                <div class="field">

                    <md-editor-component
                        :text="footerData.info.info"
                        :edit="false"
                    ></md-editor-component>
                </div>
            </section>

            <footer class="modal-card-foot">
                <div class="buttons">
                    <button class="button is-success" @click="$emit('close-modal')">Закрыть</button>
                </div>
            </footer>
        </div>
    </div>
</template>
<script>
export default {
    
    props:[
        "footerData",
    ],

    emits:["close-modal"]
}
</script>