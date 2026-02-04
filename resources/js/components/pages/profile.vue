<!-- профиль пользователя -->
<template>
    <div>
        <h1 class="title-1  has-text-centered">
            {{ user.name }}
        </h1>
        <div class="block">
            <table class="table is-bordered is-fullwidth">
                <thead>
                    <tr>
                        <th>
                            Имя
                        </th>
                        <td>
                            {{ user.name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Email
                        </th>
                        <td>
                            {{ user.email }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Активность пользователя
                        </th>
                        <td>
                            {{ user.is_active ? "Пользователь активен" : "Пользователь не активен" }}
                        </td>
                    </tr>
                    
                    <tr>
                        <th>
                            Активность пользователя в Basis
                        </th>
                        <td>
                            <span class="block">
                                {{ user.basis_active ? "Пользователь активен" : "Пользователь не активен" }}
                            </span>
                            <span class="block" v-if="currentUser.is_admin">
                                
                                <label class="labelSwitch" :data-checked="user.basis_active">
                                    <input type="checkbox"
                                        class="inputSwitch"  
                                        v-model="user.basis_active" 
                                        name="basis_active">
                                </label>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Пользовательская тема
                        </th>
                        <td>
                            <button  class='button is-outlined toggle-dark-style' v-if="user.id === currentUser.id">
                                
                                    <span class="material-icons" v-if="user.dark_style">
                                        wb_sunny
                                    </span>
                                    <span class="material-icons" v-else>
                                        nights_stay
                                    </span>
                            </button>
                            <button  class='button is-outlined toggle-dark-style' v-else>
                                <span v-if="user.dark_style">

                                    Темная
                                </span>
                                <span v-else>
                                    Светлая
                                </span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Администратор Basis
                        </th>
                        <td >
                            <div >
                                <div v-if="user.is_admin">
                                    Да
                                </div>
                                <div v-else>
                                    Нет
                                </div>
                            </div>
                            <div class="block" v-if="currentUser.is_admin">
                                
                                <label class="labelSwitch" :data-checked="user.is_admin">
                                    <input type="checkbox"
                                        class="inputSwitch"  
                                        v-model="user.is_admin" 
                                        name="is_admin">
                                </label>
                            </div>
                            
                        </td>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="field">
            <label  class="label">Список групп</label>

            <v-select
                :reduce="reduce"
                :options="groups"
                v-model="user.groupsList"
                :multiple="true"
                :disabled="!currentUser.is_admin"
            ></v-select>
        </div>
        <div class="field" v-if="currentUser.is_admin">
            <button @click="save()" 
                class="button is-success">
                Сохранить 
            </button>
        </div>
        <div class="block">
            <router-link to="/settings/suggested-changes-to-objects">
                Предложения об изменении
            </router-link>
        </div>
    </div>
</template>
<script>

import ToggleSwitch from 'primevue/toggleswitch';

export default {
    components:{ ToggleSwitch },    

    data() {
        return {
            checked: false,
            user:{},
            currentUser:{},
            groups:[]
        }
    },
    
    created() {

        axios.post(route("profile", this.$route.params.id )).then(response => {
            let data = response.data;
            
            if(data.error){
                this.$router.push("/"+data.error);
                
                return;
            }
            this.groups = data.groups
            this.user = data.user;
            this.currentUser = data.currentUser;
        }).catch(error => {
            this.$router.push("/"+error.status);
            
        })
    },
    methods: {
        reduce(option){
            return { group_id : option.id}
        },
        /**
         * Сохранение данных пользователя
         */
        save(){
            axios({
                url: route("settings.update-profile"),
                method: "post",
                data: this.user
            }).then(response => { 
                let data = response.data;
                
                data.result ? this.$toast.success(data.message) : this.$toast.error(data.message)
            }).catch(error => {
                
                // обрабока ошибкии перенаправление на странице с ней
                this.$router.push("/"+error.status);
            })
        }
    },
}
</script>
<style lang="scss">
.inputSwitch[type=checkbox]{
  height: 0;
  width: 0;
  visibility: hidden;
}

.labelSwitch {
  cursor: pointer;
  text-indent: -9999px;
  width: 50px;
  height: 30px;
  background: #f34b4b78;
  display: block;
  border-radius: 100px;
  position: relative;
}

.labelSwitch:after {
  content: '';
  position: absolute;
  top: 5px;
  left: 5px;
  width: 20px;
  height: 20px;
  background: #fff;
  border-radius: 20px;
  transition: 0.3s;
}

.labelSwitch[data-checked=true] {
  background: #bada55;
}

.labelSwitch[data-checked=true]:after {
  left: calc(100% - 5px);
  transform: translateX(-100%);
}

.labelSwitch:active:after {
  width: 40px;
}


ul#vs1__listbox {
    margin-top: 0 !important;
    margin-inline-start: 0 !important;
}


</style>