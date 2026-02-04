<template>
    <div v-if="uploaded">
        <breadcrumbs-panel
            :object="object"
            @toggle-edit="edit = !edit"
            @delete-object="deleteFolder"
            @reload-object="load(true)"
            ref="breadcrumbsPanel"
        ></breadcrumbs-panel>
        
        <h1 class="title is-3 has-text-centered m-3"> 
               {{ object.id ? object.name : "Новая папка" }}    
        </h1>
        
        <div v-if="!edit && object.children" class="block">
            <div class="field" v-for="child in object.children">
                <label class="label">
                    <router-link :to="'/objects/'+child.type+'/'+child.id">
                        {{ child.name }}
                    </router-link>
                    
                </label>
            </div>

            <div class="field">
                Права доступа будут позже
            </div>
 
        </div>

        <div v-else class="block">
             
            <div class="field">
                <v-select
                    :reduce="reduce"
                    :options="parents"
                    v-model="object.parent_id"
                    placeholder="Выберите родительскую директорию"
                ></v-select>
            </div>

            <div class="field">
                <input
                    type="text"
                    class="input"
                    v-model="object.name"
                    placeholder="Введите название">
            </div>    
        </div>

        <div class="sticky-block-button" v-if="edit">
            <button class="button is-success m-1"
                @click="save()">
                Сохранить 
            </button>
            <button class="button is-danger m-1">
                Отменить
            </button>
        </div>
        <window-delete-folder
            v-if="object.id && removal"
            :folder="object"
            @cancel-delete="removal = false"
            :folders="parents"
        ></window-delete-folder>
    </div>
</template>
<script>
import BreadcrumbsPanel from '../menu/breadcrumbs-panel.vue';
import WindowDeleteFolder from '../windows/window-delete-folder.vue';

export default {
    components:{ BreadcrumbsPanel, WindowDeleteFolder },
    data() {
        return {
            /**
             * 
             */
            object:{},

            /**
             * включение/выключение режима редактирования
             */
            edit:false,

            /**
             * массив с папками для v-select
             */
            parents: [],

            /**
             * копия оригинальных данных
             */
            origin: {},

            uploaded: false,

            /**
             * отображает окно для удаления папки
             */
            removal:false

        }
    },

    created() {
       this.load()
    },

    methods: {

        async load(reload = false){
            // запрашиваем данные папки
            
            let id =  this.$route.params.id ?  this.$route.params.id : "";

            let response = await axios.get(route("objects.folder", id));
            
            // проверяем наличие id
            if (!id) {

                // ели его нет, то статья только создается
                this.object.type = 'folder';
                this.edit = true

            }else{

                this.object = response.data.folder;
                this.origin = JSON.parse(JSON.stringify(response.data.folder));

            }
            this.parents = response.data.folders;
            if (reload) {
                this.$toast.success("Обновлено");
            }
            this.uploaded = true
        },

        /**
         * сохранение папки 
         */
        save(){

            let id = this.object.id ? this.object.id : "";

            axios.post(route("objects.folder", id), this.object).then(response => {
                let data = response.data;
                if(data.error){
                    this.$toast.error(data.error);
                    return;
                }

                this.$toast.success("Сохранено");

                // перемещаем на страницу папки, если была создана новая папка
                if (!this.object.id) {
                    this.$router.push("/objects/folder/"+data.folder.id)
                    return;
                }

                // копируем оригинал
                this.origin = JSON.parse(JSON.stringify(data.folder));
                
                // обновляем хлебные крошки
                this.$refs.breadcrumbsPanel.updateBreadcrumbs();

            })  
        },
        
        reduce(option){
            return option.id;
        },
        /**
         * удаление папки с потверждением
         */
        deleteFolder(){

            if (!window.confirm("Вы уверены что хотите удалить '"+this.object.name+"' ?")) {
                return;
            }

            // если у папки существуют дочение элемента
            // то открываем окно с подверждением перемещения/удаления дочерних элементов
           
            if (this.object.children.length > 0) {
                this.removal = true;
                return;
            }

            // если у папки не сущестувет дочерних элементов
            // то просто удаляем папку
            axios({
                    url:route("objects.delete-object", this.folder.id),
                    method:"post"

                }).then(response => {
                    if (response.data.result) {
                        this.$toast.success("Удалено");
                        this.$router.push("/");
                    }
                }).catch(error => {
                    // this.$toast.error(error.response.message);
                    // this.$router.push("/"+error.status);
                })
        }
    },
}
</script>