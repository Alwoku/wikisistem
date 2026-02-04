<!-- страница всех пользователей -->
<template>
    <div>

        <div class="is-flex is-justify-content-space-between has-block-sticky-top">
            <div class="dropdown is-hoverable">
                <div class="dropdown-trigger">
                    <button class="button" 
                            title="Обновить данные">
                        <span class="material-icons">
                            refresh
                        </span>
                    </button>
                </div>
                <div class="dropdown-menu" id="dropdown-menu4" role="menu">
                    <div class="dropdown-content">
                        <div class="dropdown-item">
                            <button @click="load(true)">
                                Обновить
                            </button>
                            
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <p class="control has-icons-right">
                    <input class="input" 
                        type="text"
                        placeholder="Поиск" 
                        v-model="search" 
                        @keyup="searchUser"/>

                    <span class="icon is-right">
                        <span class="material-icons">
                            search
                        </span>
                    </span>
                </p>
            </div>
        </div>
        <div class="box" v-if="foundUsers.length > 0">
            <div class="has-text-right">
                
                <button  @click="clearSearch()" title="Очистить поиск">
                    <span class="material-icons">
                        close
                    </span>

                </button>
            </div>
            <div v-for="user in foundUsers">
                <router-link :to="'/profile/'+user.id">
                    {{ user.name }}
                </router-link>
            </div>
        </div>

        <h1 class="title-1 has-text-centered">
            Пользователи
        </h1>

        <table class="table is-fullwidth is-bordered">
            <thead>
                <tr>
                    <th>
                        ID
                    </th>
                    <th>
                        Имя
                    </th>
                    <th>
                        Активность 
                    </th>
                    <th>
                        Доп.информация
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users[1]">
                    <td>
                        <router-link :to="'/profile/'+user.id">
                            {{ user.id }}
                        </router-link>
                    </td>
                    <td>
                        <router-link :to="'/profile/'+user.id">
                            {{ user.name }}
                        </router-link>
                    </td>
                    <td>
                        {{ user.is_active ? "активен" : "не активен"}}
                        <br>
                        {{ user.basis_active ? "активен" : "не активен"}} в системе Basis
                    </td>
                    <td>
                        <div>
                            <b>
                            {{ user.is_admin ? "Пользователь является администратором" : ""}}
                            </b>
                        </div>
                        <div v-if="user.groups.length !== 0">

                            <div v-if="user.groups.length < 5">
                                <label class="label">Список групп</label>
                                <div v-for="group in user.groups" class="pl-1">

                                    <router-link :to="'/settings/groups#'+group.group_id">
                                        {{ group.group.name }}
                                    </router-link>
                                </div>
                            </div>

                            <details v-if="user.groups.length > 5">
                                <summary>
                                    Список групп
                                </summary>
                                <div v-for="group in user.groups" >
                                    <router-link :to="'/settings/groups#'+group.group_id">
                                        {{ group.group.name }}
                                    </router-link>
                                </div>
                            </details>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <details>
            <summary>Список неактивных пользователей</summary>

            <table class="table is-fullwidth is-bordered">
                <thead>
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Имя
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users[0]">
                        <td>
                            <router-link :to="'/profile/'+user.id">
                                {{ user.id }}
                            </router-link>
                        </td>
                        <td>
                            <router-link :to="'/profile/'+user.id">
                                {{ user.name }}
                            </router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </details>

    </div>
</template>
<script>
export default {
    
    data() {
        return {
            /**
             * список пользователей
             */
            users:{},

            search: "",

            foundUsers:[]
        }
    },

    created() {
        this.load();
    },

    methods: {
        /**
         * стандартное обновление с локальной базы данных
         */
        async load(reload = false){
            let response = await axios.get(route("settings.users")).catch(error => {
                            // обработка ошибки
                            this.$router.push("/"+error.status);

                            this.$toast.error(error.response.message);
                            console.clear()
                        })
            this.users = response.data

            if (reload) {
                this.$toast.success("Успешно")
            }

        },

     
        /**
         * поиск по пользователям
         */
        searchUser(){
            if (this.search.length < 2) {

                if(this.foundUsers.length > 0) this.foundUsers = [];
                return;
            }
            
            axios({
                url:route("settings.search-users"),
                data: {
                    search:this.search
                },
                method:"post"
            }).then(response => {
                this.foundUsers = response.data.users;
            })
        },

        /**
         * Очистить поиск
         */
        clearSearch(){
            this.search = '';
            this.foundUsers = [];
        }
    },
}
</script>