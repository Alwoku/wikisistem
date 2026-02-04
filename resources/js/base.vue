<template>
    <div >
        <div v-if="loginWindow || user === null">
            <login
                @login="login"
            ></login>
        </div>
        <div v-else>
            <header-menu
                :user="user"
                @user-logout="logout"
                @update-menu="onUpdateMenu"
            ></header-menu>

        
            <div class="is-flex">
                <panel-menu
                    v-if="openMenu"
                    :user="user"
                    :open-menu="openMenu"
                    @close-menu="closeMenu"
                ></panel-menu>
                <router-view class="content" :key="$route.path"></router-view>
            </div>

            <base-footer
                :user="user"
                :read="modalNotification"
            ></base-footer>
        </div>
      
    </div>
</template>
<script>
import login from './components/login.vue';
import headerMenu from './components/menu/header-menu.vue';
import PanelMenu from './components/menu/panel-menu.vue';
import BaseFooter from './components/modules/base-footer.vue';

export default {

    components: { login, headerMenu, PanelMenu, BaseFooter },

    name: 'App',

    data() {
        return {
            /**
             * объект с данными пользователя
             */
            user:null,

            /**
             * открывает окно авторизации при отсутствии 
             * авторизованного пользователя
             */
            loginWindow:false,

            /**
             * отслеживате открытие/закрытие и фиксацию бокового меню
             */
            openMenu:null,

            modalNotification: false
        }
    },

    created() {
        this.load()

        //проверка авторизации для актуализации сессии 
        setInterval(()=>{
            this.checkedLogin(); 
        }, 10000);
        
    },

    methods: {

        load(){
            this.checkedLogin();

        },

        /**
         * проверка наличия авторизации пользователем
         * 
         */
        checkedLogin(){
            axios.get(route("checkedLogin")).then(response => {
                let data = response.data;
                
                if (data.result) {

                    // забираем данные пользователя
                    this.user = data.user;

                    // закрываем окно авторизации
                    this.loginWindow = false;

                    // забираем состояние меню
                    this.openMenu = data.user.panel_pin;

                    // 
                    this.modalNotification = data.modalNotification;

                    document.body.classList.value = this.user.dark_style ? "dark-style" : "light-style";

                    return;
                }

                if(data.message) this.$toast.error(data.message)
                this.loginWindow = true
            })    
        },


        logout(){
            this.user = null;
            this.loginWindow = true;
        },

        /**
         * авторизация пользователя
         */
        login(){
            this.checkedLogin()
            // this.loginWindow = false;
        },

        /**
         * закрытие бокового меню 
         */
        closeMenu(){
            this.openMenu = "";
        },

        /**
         * обновление состояния бокового меню
         * @param menu 
         */
        onUpdateMenu(menu){
            if (menu === this.openMenu) {
                this.openMenu = "";
                return;
            }
            this.openMenu = menu
        }
    },
};
</script>