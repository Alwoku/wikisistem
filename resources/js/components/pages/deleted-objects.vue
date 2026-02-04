<!-- страница с удаленными папками/статьями с возможностью их восстановить  -->
<template>
    <div>
        
        <div class="is-flex is-justify-content-space-between pt-1 has-block-sticky-top">
            <button @click="load(true)" title="Обновление данных списка групп">
                <span class="material-icons">
                    refresh
                </span>
            </button>

        </div>

        <h1 class="title has-text-centered">Удаленные объекты</h1>



        <div class="block">
            <aside class="menu">
                <ul class="menu-list">
                    <li v-for="(object, index) in objects">
                        <a :class="{'is-active': object.open}"
                            @click="object.open = !object.open">
                            {{ object.name }}
                        </a>

                        <ul class="menu-list" v-if="object.open">
                            <li>
                                <div class="field">
                                    <label class="label">
                                        {{ object.type === 'note' ? "Статья" : "Папка" }}
                                    </label>
                                </div>
                                <div class="field" v-if="object.parent_id != null">
                                    <label class="label">
                                        Родительская папка
                                    </label>
                                    <router-link :to="'/objects/folder/'+object.parent_id" target="_blank">
                                        {{ object.parent.name }} 
                                    </router-link>
                                </div>
                                <div v-if="object.type == 'note'">
                                    <div class="field">
                                        <label class="label">
                                            Version №{{ object.version }}
                                        </label>
                                    </div>
                                    <div class="field">
                                        <label class="label">
                                            Контент
                                        </label>

                                        <details v-if="object.content">
                                            <summary> 
                                                <i>
                                                    Посмотреть подробности
                                                </i>
                                            </summary>
                                           <div class="block p-3">
                                                <v-md-editor
                                                    mode="preview"
                                                    v-model="object.content.content "></v-md-editor>
                                                
                                           </div>
                                            
                                        </details>
                                        <div v-else>
                                            Пусто
                                        </div>
                                    </div>
                                </div>

                                <div class="field">
                                    <label class="label">Автор</label>
                                    <router-link :to="'/profile/'+object.user_id" target="_blank">
                                        {{ object.user.name }}
                                    </router-link>
                                    
                                </div>

                                <div class="is-flex  buttons-deleted-objects">
                                    <button class="button m-1 is-success" @click="preparingRestore(object, index)">
                                        Восстановить 
                                    </button>
                                    <button class="button m-1 is-danger" @click="forceDelete(object, index)">
                                        Удалить окончательно
                                    </button>
                                    
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </aside>
        </div>
        <div class="modal"
            :class="{'is-active': windowParent}">
            <div class="modal-background" @click="cancel()"></div>
            <div class="modal-card">
                <header class="modal-card-head">
                    <p class="modal-card-title">
                        Восстановление объекта ID:{{ restoringObject.id }} {{ restoringObject.name }}
                    </p>
                    <button class="delete" aria-label="close"  @click="cancel()"></button>
                </header>
                <section class="modal-card-body">
                    
                    <label class="label">
                        Родительская папка у данного объекта удалена.
                        <p>
                            Куда разместить '{{ restoringObject.name }}' ?
                        </p>
                    </label>
                    <div class="block is-size-6 has-text-grey">
                        По умолчаниию объект останется без родителя
                    </div> 

                    <aside class="menu">

                        <ul class="menu-list">
                            <li>
                                <a :class="{'is-active': toggle == 'exists'}"
                                    @click="toggle = 'exists'">
                                    Выбрать из существующих
                                </a>
                                <ul v-if="toggle === 'exists'">
                                    <li>
                                        <div class="block">
                                            <v-select
                                                :reduce="reduce"
                                                :options="folders"
                                                v-model="parentId"
                                                placeholder="Выберите родительскую директорию"
                                            ></v-select>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a :class="{'is-active': toggle == 'new'}" 
                                    @click="toggle = 'new'">
                                    Создать новую папку
                                </a>
                                <ul>
                                    <li v-if="toggle === 'new'">
                                        <div class="block">

                                            <div class="field">
                                                <v-select
                                                    :reduce="reduce"
                                                    :options="folders"
                                                    v-model="newParent.parent_id"
                                                    placeholder="Выберите родительскую директорию"
                                                ></v-select>
                                            </div>

                                            <div class="field">
                                                <input
                                                    type="text"
                                                    class="input"
                                                    v-model="newParent.name"
                                                    placeholder="Введите название">
                                            </div>    
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </aside>
                </section>
                <footer class="modal-card-foot">
                    <div class="buttons">
                        <button class="button is-success" @click="restore()">Подтвердить</button>
                        <button class="button" @click="cancel()">Отмена</button>
                    </div>
                </footer>
            </div>
        </div>

    </div>
</template>
<script>
export default {
    data() {
        return {
            /**
             * список удаленных объектов
             */
            objects: [],

            /**
             * массив существующих папкок для пересещения
             */
            folders: [],

            /**
             * открывает окно для выбора/создания родительской директории
             */
            windowParent: false,


            /**
             * для создания новой директории
             */
            newParent: null,


            /**
             * выбранная родительская папка 
             */
            parentId: null,

            
            /**
             * переключение режимов в окне выбора родительской директории
             */
            toggle: 'exists',

            /**
             * объект для восстановления
             */
            restoringObject: {},

            /**
             * массив восстановленных объектов
             */
            restoredObjects:[],
        }
    },
    
    created() {
        this.load();
    },

    methods: {
        /**
         * подгрузка данных
         */
        async load(reload = false){
            let response = await axios.get(route("settings.deleted-objects")).catch(error => {
                        // обработка ошибки
                        this.$router.push("/"+error.status);
                        
                        this.$toast.error(error.message);
                        console.clear()
                    });
            
            this.objects = response.data.objects;

            this.folders = response.data.folders;

            if (reload) {
                this.$toast.success("Готово");
            }
        },

        preparingRestore(object, i){
            if (!window.confirm("Вы уверены что хотите восстановить '"+object.name+"' ?")) {
                return;
            }

            this.restoringObject = object;
            this.restoringObject.index = i;

            

            if (object.parent_id !== null && object.parent.deleted_at !== null) {
                this.newParent = {};
                this.windowParent = true;
                return;
            }

            this.restore();
        },

        /**
         * восстановление объекта
         * @param object 
         */
        async restore(){
            
            if (this.toggle === 'new' && this.newParent.name) {
                this.saveNewFolder();
            }
        
            let data =  {
                        update_parent: this.windowParent,
                        parentId: this.parentId,
                        id: this.restoringObject.id,
                    }

            let response = await axios.post(route("objects.restoring-object"),
                    data).catch(error => {
                        // обработка ошибки
                        // this.$router.push("/"+error.status);
                        console.log(error);
                        
                        this.$toast.error(error.message);
                        // console.clear()
                    });

            if (response.data.result) {

                this.$toast.success("Объект '"+this.restoringObject.name+"' восстановлен");
                
                this.restoredObjects.push({
                    id: this.restoringObject.id,
                    name: this.restoringObject.name
                });

                this.objects.splice(this.restoringObject.index, 1);
                this.restoringObject = {};
                this.windowParent = false;
                return;
            }
            
        },

         /**
         * создание новой папки
         */
         async saveNewFolder(){

            let response = await axios.post(route("objects.folder"), this.newParent)

            if(response.data.error){
                this.$toast.error(response.data.error);
                return;
            }

            this.parentId = response.data.folder.id;
        },

        /**
         * окончательное удаление без возможности восстановить
         * @param object 
         * @param i 
         */
        forceDelete(object, i){

            if (!window.confirm("Вы уверены что хотите удалить '"+object.name+"' ?")) {
                return;
            }

            axios.post(route("objects.force-delete"), {objectId: object.id}).then(response =>{
                if (response.data.result) {
                    this.$toast.success("Объект '"+object.name+"' удален");
                    this.objects.splice(i, 1);
                }
                console.log(response.data);
                
            }).catch(error => {
                        // // обработка ошибки
                        // this.$router.push("/"+error.status);
                        
                        // this.$toast.error(error.message);
                        // console.clear()
            })
        },

        reduce(option){
            return option.id;
        },

        /**
         * cancel restoring
         */
        cancel(){
            this.restoringObject = {};
            this.newParent = null;
            this.parentId = null;
            this.windowParent = false;
        }
    },
}
</script>
<style lang="scss">
.buttons-deleted-objects>.button {
    max-width: 200px;
}
</style>