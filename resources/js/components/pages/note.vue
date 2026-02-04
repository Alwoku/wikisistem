<!-- создание/редактирование/чтение статей -->
<template>
    <div v-if="uploaded">
        <breadcrumbs-panel
            :object="object"
            :edit="edit"
            @toggle-edit="edit = !edit"
            @delete-object="deleteNote"
            @reload-object="load(true)"
            @open-suggested-changes="windowChangesObject = !windowChangesObject"
            @open-list-suggested-changes="windowListSuggested = !windowListSuggested"
            ref="breadcrumbsPanel"
        ></breadcrumbs-panel>
        
        <h1 class="title is-3 has-text-centered m-3"> 
               {{ object.id ? object.name : "Новая статья" }}    
        </h1>

        <div class="is-size-6 has-text-grey">
            Ver.{{ object.version }}
            <div class="is-size-6">
                
            (нужно ли перенести версию в шапку?)
            </div>
        </div>
        
        <div class="field" v-if="edit">
            <v-select
                :reduce="reduce"
                :options="folders"
                :disabled="!edit"
                v-model="object.parent_id"
                placeholder="Выберите родительскую директорию"
            ></v-select>
        </div>

        <div class="field">
            <input v-if="edit"
                type="text"
                class="input"
                v-model="object.name"
                placeholder="Введите название">
        </div>    

        <div class="field">
            Здесь будут теги #tag #tag
        </div>

        <div class="field">
            <md-editor-component
                :text="object.text"
                @update:text="updateText"
                :edit="edit"
            ></md-editor-component>
        </div>

        <div class="field">
            Права доступа будут позже
        </div>
 

        <!-- todo добавть всплывающее окно для добавления комментария при обновлении версии -->

        <div class="sticky-block-button" v-if="edit">
            <button class="button is-success m-1"
                @click="save()">
                Сохранить 
            </button>
            <button class="button is-danger m-1">
                Отменить
            </button>
        </div>

        <div v-if="windowChangesObject">

            <window-suggested-changes
                :data="object"
                :folders="folders"
                @close-window="windowChangesObject = false"
            ></window-suggested-changes>
        </div>

        <div v-if="windowListSuggested">
            <window-list-suggested-changes
                :object="object"
                :suggested-changes="object.suggested_changes"
                @close-window="windowListSuggested = false"
            ></window-list-suggested-changes>
        </div>
    </div>
</template>
<script>
import WindowSuggestedChanges from '../windows/window-suggested-changes.vue';
import BreadcrumbsPanel from '../menu/breadcrumbs-panel.vue';
import WindowListSuggestedChanges from '../windows/window-list-suggested-changes.vue';

export default {

    components:{
        BreadcrumbsPanel,
        WindowSuggestedChanges,
        WindowListSuggestedChanges
    },
    data() {
        return {
            /**
             * данные статьм
             */
            object:{}, 

            /**
             * включение/выключение режима редактирования
             */
            edit:false,

            /**
             * массив с папками для v-select
             */
            folders: [],
            
            /**
             * копия оригинальных данных
             */
            origin: {},

            uploaded: false,

            /**
             * окно с возможностью предложить изменения в статье
             */
            windowChangesObject: false,
            
            /**
             * окно со списком предложенных изменений 
             */
            windowListSuggested: false
        }
    },
    created() {
        this.load()
        
        setInterval(()=>{
            this.load()
        }, 15000)
    },
    
    methods: {

        async load(reload = false){

        console.log("test note");
        
            let id = this.$route.params.id ? this.$route.params.id : "";

            // запрашиваем данные статьи

            let response = await axios.get(route("objects.note", id)).catch(error =>{
                            console.log(error);
                            
                
                            // // обработка ошибки
                            this.$router.push("/"+error.status);
                            if (error.response) {
                                this.$toast.error(error.response.data.message);
                                
                            }
                        });

            // проверяем наличие id
            if (!id) {

                // ели его нет, то статья только создается
                this.object.type = 'note';
                this.object.version = 1

                this.edit = true

            }else{

                this.object = response.data.note;
                this.origin = JSON.parse(JSON.stringify(response.data.note));

            }
            
            this.folders = response.data.folders;

            if (reload) {
                this.$toast.success("Обновлено");
            }
            
            this.uploaded = true
        },
        /**
         * сохранение данных статьи
         */
        save(updateVersion = false){

            if (updateVersion) {
                this.object.update_version = true;
            }

            let id = this.object.id ? this.object.id : "";
            axios.post(route("objects.note", id), this.object).then(response => {
                let data = response.data;
                if(data.error){
                    this.$toast.error(data.error);
                    return;
                }

                this.$toast.success("Сохранено");
                if (!this.object.id) {
                    this.$router.push("/objects/note/"+data.note.id)
                    return;
                }


                if (updateVersion) {
                    this.object.update_version = false;
                }

                this.origin = JSON.parse(JSON.stringify(data.note));
                
                this.$refs.breadcrumbsPanel.updateBreadcrumbs();

            })
            
        },

        reduce(option){
            return option.id;
        },

        /**
         * удаление статьи
         */
        deleteNote(){

            if (!window.confirm("Вы уверены что хотите удалить '"+this.object.name+"' ?")) {
                return;
            }

            axios({
                url:route("objects.delete-object", this.object.id),
                method:"post"
                
            }).then(response => {
                    if (response.data.result) {
                        this.$toast.success("Удалено");
                        this.$router.push("/");
                    }
                }).catch(error => {
                    this.$toast.error(error.response.message);
                    this.$router.push("/"+error.status);
                })
        },

        updateText(text){
            this.object.text = text;
        }
    },
}
</script>
<style>

.CodeMirror-cursor {
    border-left: 1px solid white;
    border-right: 1px solid white;
  }
</style>