<template>
    <div class="modal is-active">
        <div class="modal-background" @click="cancel()"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <p class="modal-card-title">
                    Удаление папки {{ folder.name }}
                </p>
                <button class="delete" 
                        aria-label="close"  
                        @click="cancel()"></button>
            </header>
            <section class="modal-card-body">
                <div class="block" v-if="deletingBranch">
                    <label class="label">
                        Удалить всю ветку папок и статей?
                    </label>
                </div>
                <div class="block" v-else>
                    <label class="label">
                        Куда переместить содержимое?
                    </label>
                    <div class="is-size-6 has-text-grey">
                        По умолчаниию дочерние элементы остаются без родителя
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
                                                    v-model="newFolder"
                                                    placeholder="Выберите родительскую директорию"
                                                ></v-select>
                                            </div>

                                            <div class="field">
                                                <input
                                                    type="text"
                                                    class="input"
                                                    v-model="newFolder.name"
                                                    placeholder="Введите название">
                                            </div>    
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </aside>
                </div>
            </section>
            <footer class="modal-card-foot">
                <div class="buttons" v-if="deletingBranch">
                    <button class="button is-success" @click="deletingBranch = false">
                        Нет
                    </button>
                    <button class="button" @click="deleteFolder()">
                        Да
                    </button>
                    <button class="button" @click="cancel()">
                        Отмена
                    </button>
                </div>
                <div class="buttons" v-else>
                    <button class="button is-success" @click="confirmDelite()">
                        Подтвердить 
                    </button>
                    <button class="button" @click="cancel()">
                        Отмена
                    </button>
                </div>
            </footer>
        </div>
        
    </div>
</template>
<script>
export default {
    props:{
        folder:{
            type:Object,
            required:true,
        },
        /**
         * массив родительских папок
         */
        folders:{
            type:Array,
            required:false
        }

    },
    data() {
        return {
            /**
             * замена родительской папки 
             * для дочерних элементов
             */
            parentId: null,

            /**
             * удалять ли всю ветку
             */
            deletingBranch: true,

            toggle: 'exists',

            newFolder: {},

        }
    },

    emits:["cancel-delete"],

    methods: {
        /**
         * 
         */
        cancel(){
            this.parentId = null;
            this.deletingBranch = false;

            this.toggle = 'exists';

            this.newFolder = {};
            this.$emit("cancel-delete");
        },

        /**
         * создание новой папки
         */
        async saveNewFolder(){
            let response = await axios.post(route("objects.folder"), this.newFolder)
            if(response.data.error){
                this.$toast.error(data.error);
                return;
            }
            this.parentId = response.data.folder.id;
        },

        confirmDelite(){
            if (this.toggle === 'new') {
                this.saveNewFolder();
            }
            this.deleteFolder();
        },

        /**
         * удаление папки
         */
        deleteFolder(){
            let data = {
                deleteBranch: this.deletingBranch,
                parent_id: this.parentId
            }


            axios({
                url:route("objects.delete-object", this.folder.id),
                data: data,
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
        },


        reduce(option){
            return option.id;
        },
    },
}
</script>