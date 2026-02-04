<template>
    <div>
        <div class="is-flex is-justify-content-space-between has-block-sticky-top">
            <button @click="load()" title="Обновление данных">
                <span class="material-icons">
                    refresh
                </span>
            </button>

            <button title="Создать версию"
                @click="createVersion()">
                <span class="material-icons has-text-success">
                    add
                </span>
            </button>
        </div>
        <div class="block" v-if="updateVersion.edit">
            <div class="field">
                <div class="field is-horizontal">
                    <div class="field-label is-normal">
                        <label class="label">Ver.</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                        <p class="control">
                            <input class="input" 
                                v-model="updateVersion.version" 
                                type="text"
                                placeholder="Введите номер версии" />
                        </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="field">
                <label class="label">
                    Информация об изменениях
                </label>
                <md-editor-component
                    :text="updateVersion.info"
                    @update:text="text => { updateVersion.info = text}"
                    :edit="updateVersion.edit"
                ></md-editor-component>
            </div>
            <div class="field" v-if="updateVersion.admin_info != null">
                <label class="label">
                    Информация для Администраторов Basis
                </label>
                <md-editor-component
                    :text="updateVersion.admin_info"
                    @update:text="text => { updateVersion.admin_info = text}"
                    :edit="updateVersion.edit"
                ></md-editor-component>
            </div>
            <div v-else class="field">
                <button @click="addAdminInfo()">
                    Добавить информацию для админов Basis
                </button>
            </div>
            <div class="buttons">
                <button class="button is-success" @click="save()">
                    Сохранить
                </button>
                <button class="button" @click="cancelUpdate()">
                    Отменить
                </button>
            </div>
        </div>
        <div class="field" v-for="version in history">
            <div class="block" v-if="version.active">
                <div class="block">
                    
                    <header class="modal-card-head">
                        <b class="modal-card-title  p-3">
                            Версия {{ version.version}}
                        </b>
                        <button class="button" @click="updateVersionInfo(version)">
                            <span class="material-icons">
                                edit
                            </span>
                        </button>
                    </header>
                    <md-editor-component
                        :text="version.info"
                        :edit="false"
                    ></md-editor-component>
                </div>
                <div class="block" v-if="version.admin_info">
                    <header class="modal-card-head">
                        <b class="modal-card-title p-3">
                            Информация для Администраторов Basis
                        </b>
                    </header>

                    <md-editor-component
                        :text="version.admin_info"
                        :edit="false"
                    ></md-editor-component>
                </div>
            </div>
            <div v-else>
                <details>
                    <summary  class="modal-card-head">
                        Версия {{ version.version}}
                        <span class="material-icons">
                            expand_more
                        </span>
                    </summary>
                    <div class="block">
                        <md-editor-component
                            :text="version.info"
                            :edit="false"
                        ></md-editor-component>
                    </div>
                    <div class="block">
                        <header class="modal-card-head">
                            <b class="modal-card-title">
                                Информация для Администраторов Basis
                            </b>
                        </header>

                        <md-editor-component
                            :text="version.info"
                            :edit="false"
                        ></md-editor-component>
                    </div>
                </details>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    
    data() {
        return {
            /**
             * массив с информацией об изменениях
             */
            history: [],

            /**
             * версия для изменения
             */
            updateVersion: {},

        }
    },

    created() {
        this.load(); 
    },

    methods: {
        load(){
            axios.get(route("settings.history-of-changes")).then(response =>{
                
                this.history = response.data.history;

            }).catch(error => {
                // обработка ошибки
                this.$router.push("/"+error.status);

                console.clear()
            })
        },

        /**
         * создание новой версии
         */
        createVersion(){
            this.updateVersion = {
                version: "",
                info: "",
                active: true,
                edit: true
            }
        },


        /**
         * изменение информации у активной версии
         */
        updateVersionInfo(version){

            // проверяем актуальность версии
            if (!version.active) {
                this.$toast.error("Ошибка. Версия неактивна");
                return;
            }

            // копируем версию так, чтобы не внести изменения в оригинал
            this.updateVersion = JSON.parse(JSON.stringify(version));
            this.updateVersion.edit = true;
        },


        /**
         * сохранение изменений
         */
        save(){
            let id = this.updateVersion.id ? this.updateVersion.id : "";

            axios.post(
                route("settings.update-history-of-changes", id), 
                this.updateVersion).then(response => {
                    if (response.data.error) {
                        this.$toast.error(response.data.error);
                        return;
                    }
                    

                    this.$toast.success(response.data.message);
                    this.history = response.data.history;
                    
                    this.updateVersion = {};
                }).catch(error => {
                    if (error.response) {
                        let data = error.response.data;

                        var errors = data.errors;

                        for (var key in errors) {
                            toastr.error(errors[key]);
                        }

                        return;
                    }
                    this.$router.push("/"+error.status);

                    console.clear()
                })
        },


        /**
         * отмена изменений
         */
        cancelUpdate(){
            this.updateVersion = {};
        },

        addAdminInfo(){
            this.updateVersion.admin_info = "";
        }
    },
}
</script>