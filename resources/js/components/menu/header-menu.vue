<template>
    <div>

        <nav class="navbar" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">


                <a class="navbar-item">

                    <div class="navbar-item" @click="switchingMenu('objects')">
                        <span class="material-icons">
                            menu
                        </span>
                    </div>
                </a>
                        
                <a class="navbar-item">

                    <div class="navbar-item" @click="switchingMenu('favorites')">
                        <span class="material-icons">
                        bookmark_border
                        </span>
                    </div>
                </a>
                <a class="navbar-item" v-if="user.is_admin">

                    <div class="navbar-item" @click="switchingMenu('settings')">
                        <span class="material-icons">
                            settings
                        </span>
                    </div>
                </a>
                <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false">
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                    <span aria-hidden="true"></span>
                </a>
            </div>
            <div class="navbar-menu">

                
                <div class="navbar-start"></div>
                <div class="navbar-center">
                    <div class="navbar-item">
                        
                        <div class="control has-icons-right">
                            <input class="input"  
                                placeholder="Поиск" 
                                autofocus
                                v-model="search"
                                @keyup.enter="redirectInSearchPage()"
                                @keyup="globalFinder"/>
                            <span class="icon is-right">
                                <span class="material-icons">
                                    search
                                </span>
                            </span>
                        </div>
                        <div class="box resul-block-global-finder" v-if="foundObjects.length > 0">
                            <div v-for="(obj, index) in foundObjects" :key="index">

                                <router-link :to="'/objects/'+obj.type+'/'+obj.id" class="is-flex p-1">
                                    <span class="material-icons" v-if="obj.type == 'note'">
                                        article
                                    </span>
                                    <span class="material-icons" v-else>
                                        folder
                                    </span>
                                    <span>
                                        {{ obj.name }}
                                    </span>
                                    
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="navbar-end">

                    <div class="navbar-item has-dropdown is-hoverable">
                        <a class="navbar-link">
                            <div class="navbar-item-username">
                                <div class="navbar-item-username-content">
                                    <span class="material-icons">
                                        account_circle
                                    </span>
                                </div>

                            </div>
                        </a>

                        <div class="navbar-dropdown profil-menu">
                            <div class="navbar-item">
                                <b>
                                    {{ user.name }}
                                </b>
                            </div>
                            

                            <router-link :to="'/profile/'+user.id" class="navbar-item">
                                Профиль
                            </router-link>
                            <!-- @if(session()->has("oldUser".user.getKey())) -->
                                <a @click="goBack()" class="navbar-item">
                                    Вернуться <br> 
                                    в свой профиль
                                </a>
                            <!-- @endif -->

                            <a href="" class="navbar-item">
                                Автосохранения
                            </a>

                            <div class="navbar-item is-flex">
                                <button  @click="toggleStyle">
                                   
                                    <span class="material-icons" v-if="user.dark_style">
                                        wb_sunny
                                    </span>
                                    <span v-else class="material-icons">
                                        nights_stay
                                    </span>
                                </button>
                            </div>

                            <a @click="logout()" class="navbar-item">
                                Выход
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </nav>
                
    </div>
</template>
<script>
import axios from 'axios';

export default {
    
    props:{
        user:{
            type: Object,
            required:true,
            default: Object
        }
    },

    data() {
        return {
            /**
             * 
             */
            search:"",

            /**
             * 
             */
            foundObjects:[]
        }
    },

    emits:["user-logout", "update-menu"],

    mounted() {
    },
    methods: {
        logout(){
            axios.post(route("logout")).then(response => {
                this.$toast.success(response.data.messages)
                this.$emit("user-logout");
            })
        },

        goBack(){
            
        },
        redirectProfile(){
            return route('profile',this.user.id);
        },

        toggleStyle(){
            axios({
                url: route("toggle-style-user"),
                method:"post"
            }).then(response => {
                this.user.dark_style = response.data;
                // смена класса для применения стиля
                document.body.classList.value = this.user.dark_style ? "dark-style" : "light-style"
            })
        },

        /**
         * фиксиция состояния меню у пользователя 
         * переключение меню
         * @param menu 
         */
        switchingMenu(menu){
            axios.post(route("panel-pin"), { menu: menu});
            this.$emit('update-menu', menu);
        },

        /***
         * 
         */
        async globalFinder(){
            
            if(this.search.length < 2){
                if(this.foundObjects.length > 0) this.foundObjects = [];
                return;
            }

            let response = await axios.post(route("objects.global-finder"), { search: this.search});
            
            this.foundObjects = response.data;
        },

        redirectInSearchPage(){
            
            this.$router.push("/objects/global/finder/"+this.search);
            this.search = "";
            this.foundObjects = [];

        }
    },
}
</script>
<style lang="scss">
    .resul-block-global-finder{
        position: fixed;
        top: 50px;
        width: 260px;
        max-height: 300px;
        overflow: auto;
    }
</style>