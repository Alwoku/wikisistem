<!-- обход всех существующих/не существующих групп для их редактирования/чтения -->
<template>
    <div>       
        <li v-for="(group, index) in groupsList">
            <a :class="{'is-active': group.open}"
                @click="group.open = !group.open"
                :ref="group.id">
                {{ group.name ? group.name : "Новая группа" }}
                
            </a>
            <ul class="menu-list" v-if="group.open">
                <li>
                    <div class="block">
            
                        <div class="field">
                            <input v-if="group.edit" type="text" 
                                    class="input" 
                                    v-model="group.name" 
                                    placeholder="Название группы">
                            <span v-else>{{ group.name }}</span>
                        </div>
                        <div class="field">
                            <label class="label">Пользователи</label>
                                <v-select
                                    :reduce="reduce"
                                    v-model="group.usersList"
                                    :options="options.users"
                                    label="label"
                                    :multiple="true"
                                    :disabled="!group.edit"
                                    placeholder="Выберите список пользователей"
                                />
                        </div>
                        <div class="field">
                            <label class="label">Стартовая страница</label>

                            <v-select
                                :reduce="option => {return option.id}"
                                v-model="group.default_index"
                                :options="options.pages"
                                :disabled="!group.edit"
                                placeholder="Выберите стартовую страницу"
                            />
                        </div>

                        <div class="field">
                            <label class="label">Оповещения</label>
                            будет чуть позже
                        </div>
                        
                        <div class="field">
                            <label class="label">Список объектов</label>
                            будет чуть позже
                        </div>

                        <div class="is-flex buttons-create-group">
                            <button @click="save(group, index)" 
                                    class="button is-success m-1" 
                                    v-if="group.edit">
                                Сохранить 
                            </button>
                            <button v-else  
                                    class="button"
                                    @click="groupEdit(group)">
                                Редактировать
                            </button>
                            <button @click="deleteGroup(index)" class="button is-danger m-1">
                                Удалить 
                            </button>
                            <button  v-if="group.edit" @click="cancelEdit(group)" class="button">
                                cancel
                            </button>
                        </div>
                    </div>
                </li>
            </ul>
        </li>
        
    </div>
</template>
<script>



export default {
    props:{

        options:{
            type:Object,
            required:false,
        },
        // список групп
        groupsList:{
            type: Array,
            required: true,
        }
    },

    emits:["save-group"],

    data() {
        return {
            editGroup:[]
        }
    },

    methods: {
        /**
         * сохранение изменений / сохранение новой группы
         * @param group 
         * @param i 
         */
        save(group, i){
            this.$emit("save-group", {
                group: group,
                index: i
            });
        },

       
        /**
         * Удаление группы
         * @param index  
         */
        async deleteGroup(i){

            // если группа существует 
            if (this.groupsList[i]['id']) {
                //удаление группы
                let response = await axios.post(route("settings.delete-group"), 
                            this.groupsList[i]).catch(error => {
                                //обрабатываем ошибки при наличии                     
                                this.$router.push("/"+error.status);

                                this.$toastr.error("Ошибка");
                                console.clear()
                                
                                
                            })  
                //выводим сообщение 
                this.$toast.info(response.data.message)
            }

            // удаляем группу из массива
            this.groupsList.splice(i, 1);
            
        },

        /**
         * открывает режим редактирования для групп
         * @param group 
         */
        groupEdit(group){

            // если группа существует, то сохраняем 
            // ее данные для возврата при отмене редактирования

            if (group.id) {
                this.editGroup[group.id] = group;
            }


            group.edit = true;
        },

        /**
         * отмена режима редактирования 
         * @param group 
         */
        cancelEdit(group){

            // если группа существует и существуют сохраненные данные
            // возвращаем старые данные

            if (group.id && this.editGroup[group.id]) {

                let old = this.editGroup[group.id];
                
                for(let key in group){
                    group[key] = old[key] ? old[key] : null;
                }

                this.editGroup.splice(group.id, 1);
                
            }
            // закрываем режим редактирования 
            group.edit = false;
        },

        scrollTo(groupId){
            
            this.$refs[groupId][0].scrollIntoView({alignToTop:true})
        },  

        reduce(options){
            return { user_id : options.id}
        }
    },
}
</script>
<style lang="scss">
    .buttons-create-group>.button{
        max-width: 150px;
    }
</style>