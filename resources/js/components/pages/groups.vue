<!-- создание/редактирование групп пользователей -->
<template>
    <div>

        <div class="is-flex is-justify-content-space-between pt-1 has-block-sticky-top">
            <button @click="load()" title="Обновление данных списка групп">
                <span class="material-icons">
                    refresh
                </span>
            </button>

            <button title="Создать новую группу"
                @click="addGroup()">
                <span class="material-icons has-text-success">
                    add
                </span>
            </button>
        </div>

        <h1 class="title  has-text-centered">Группы</h1>

        <div class="block">
            <aside class="menu">
                <ul class="menu-list">

                    <elements-groups
                        :groups-list="groups"
                        :options="options"
                        @save-group="save"
                        ref="groupsList"
                    ></elements-groups>
                    <elements-groups
                        :groups-list="newGroups"
                        :options="options"
                        @save-group="save"
                    ></elements-groups>
                </ul>
            </aside>
        </div>
    </div>
</template>
<script>
import { nextTick } from 'vue'

export default {
    
    data() {
        return {
            groups: [],
            options: {},
            onlyRead: true,
            newGroups: [],
        }
    },
    
    created() {
        this.load()
    },

    methods: {

        /**
         * обновление данных групп
         */
        load(){
            // запрашиваем данные групп
            axios.get(route("settings.groups")).then(response => {
            
                this.groups = response.data.groups;
                this.options = response.data.options; 

                if (this.$route.hash) {
                    let id = this.$route.hash.split("#")[1];

                    this.groups.forEach((group) => {
                            
                        if (group.id == id) {
                            
                            group.open = true;
                            nextTick(()=>{
                                this.$refs.groupsList.scrollTo(group.id)
                            })
                        
                            return;
                        }
                    })
                }
                
            }).catch(error => {
                // если прав доступа не достаточно 
                // делаем редирект 
                this.$router.push("/"+error.status);
                // console.log(error);
                
            });
        },

        /**
         * открытие/закрытие элемента группы
         */
        toggle(group){
            this.$set(group, "open", !group.open);
        },

        /**
         * добавление новой группы
         */
        addGroup(){
            // todo прикрутить автоскролл до новой группы
            this.newGroups.push({
                name : "",
                default_index : null,
                open: true,
                edit: true,
            })
        },

        /**
         * сохранение группы
         * @param group 
         */
        async save(dataGroup){

            let response = await axios.post(route("settings.update-group"),
                    dataGroup.group).catch(error => {
                        
                        // при отсутствии прав доступа 
                        // (на бекенде используется abort функуция)
                        // перемещаем пользователя на страницу ошибки 
                        this.$router.push("/"+error.status);

                        this.$toast.error("Ошибка");
                        console.clear()
                    });
            
            let data = response.data;
            
            // если существует ошибка выводим ее
            if (!data.result) {
                this.$toast.error(data.error);
                return;
            }


            // если группы не существовало
            if (!dataGroup.group.id) {

                // добавляем id 
                dataGroup.group.id = data.group.id;

                // закрываем режим редактирования
                dataGroup.group.edit = false;

                // обновляем список существующих групп
                this.groups.push(dataGroup.group);
                
                // удаляем группу из массива создаваемых 
                this.newGroups.splice(dataGroup.index, 1);
            }
            this.$toast.success("Сохранено")
        }
    },
}
</script>
<style lang="scss">
    
    .fixed-button-right {
        position: fixed;
        right: 20px;
        bottom: 20px;
    }
</style>