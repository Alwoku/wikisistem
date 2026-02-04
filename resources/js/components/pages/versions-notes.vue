<template>
    <div>
        <div class="is-flex is-justify-content-space-between has-block-sticky-top">
            <div class="is-flex ">

                <router-link :to="'/objects/note/'+note.id"
                    title="вернуться на страницу статьи" 
                    class="button m-1">

                    <span class="material-icons">
                        arrow_back
                    </span>
                </router-link>
                <button @click="load()" class="button m-1" title="Обновление данных списка групп">
                    <span class="material-icons">
                        refresh
                    </span>
                </button>
            </div>
            <div v-if="diffVersion">
                <button class="button" @click="diffVersion = null">
                    Скрыть 
                </button>
            </div>
        </div>
        <div class="block" v-if="diffVersion">
            <table class="table is-fullwidth">
                <thead>
                    <tr>
                        <th>
                            Выбранная версия 
                        </th>
                        <th>
                            Актуальная версия
                        </th>
                    </tr>
                </thead>
            </table>
            <Diff
                language="markdown"
                :prev="diffVersion.content"
                :current="note.content.content"
            />
        </div>
        <section class="hero">
            <div class="hero-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                ID
                            </th>
                            <th>
                                Пользователь
                            </th>
                            <th>
                                Version
                            </th>
                            <th>
                                Дата
                            </th>
                            <th>

                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="version in versions" >
                            <td @click="openWindowRollBack(version)">
                                {{ version.id }}
                            </td>
                            <td @click="openWindowRollBack(version)">
                                {{ version.user.name }}
                            </td>
                            <td @click="openWindowRollBack(version)">
                                {{ version.version }}
                            </td>
                            <td @click="openWindowRollBack(version)">
                                {{ version.created_at }}
                            </td>
                            <td>
                                <RadioButton v-model="diffVersion" :inputId="version.id"  :value="version" />
                            </td>
                            <td>
                                <button class="button" @click="openWindowRollBack(version)">
                                    Откатить версию
                                </button>
                               
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="modal is-active" v-if="windowRollBack">
            <div class="modal-background" @click="closeWindowRollBack()"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    Откат версии статьи:  {{ note.name }}
                    <br>
                    Ver. {{ versionRollBack.version }}
                </header>

                <section class="modal-card-body">

                    <md-editor-component
                        :text="versionRollBack.content"
                        :edit="false"
                    ></md-editor-component>
                </section>

                <footer class="modal-card-foot">
                    <div class="buttons">
                        <button @click="rollback()" class="button is-succe">
                            Откатить 
                        </button>
                        <button @click="closeWindowRollBack()">
                            Отмена
                        </button>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>
<script>
import RadioButton from 'primevue/radiobutton';

export default {

    components:{RadioButton},

    data() {
        return {
            /**
             * массив с данными о предыдущих 
             * версиях статьи
             */
            versions:[],

            /**
             * оригинал статьи
             */
            note:{},
            
            /**
             * версия для сравнения 
             */
            diffVersion: null,

            /**
             * 
             */
            windowRollBack:false,

            /**
             * 
             */
            versionRollBack: null
        }
    },

    created() {
        this.load();
    },

    methods: {
        /**
         * загрузка данных
         */
        async load(){
            
            let response = await axios.get(route("objects.versions", this.$route.params.id)
                    ).catch(error =>{
                            // обработка ошибки
                            this.$router.push("/"+error.status);

                            this.$toast.error(error.response.message);
                            console.clear() 
                    });
            
            this.versions = response.data.versions;
            this.note = response.data.note;
        },

        /**
         * открывает окно с данными 
         * для отката версии
         * @param version 
         */
        openWindowRollBack(version){
            this.versionRollBack = version;
            this.windowRollBack = true;
        },

        /**
         * закрывает окно с данными
         */
        closeWindowRollBack(){
            this.versionRollBack = null;
            this.windowRollBack = false;
        },

        /**
         * откатывает версию контента
         */
        rollback(){
            
            // проверяем совпадение версий
            if (this.note.version !== this.versionRollBack.version) {
                
                // откатываем версию, если они не совпадают
                this.note.update_version = true;
                this.note.version = this.versionRollBack.version;
            }

            // изменяем контент
            this.note.text = this.versionRollBack.content;

            // сохраняем данные
            axios.post(route("objects.note", this.note.id), this.note).then(response => {
                let data = response.data;
                if(data.error){
                    this.$toast.error(data.error);
                    return;
                }

                this.$toast.success("Сохранено");
                this.$router.push("/objects/note/"+this.note.id)
            });
        },

    },
}
</script>
