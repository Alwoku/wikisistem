<!-- окно авторизации -->
<template>
    <div>
        <div id="content" class="vue login-page-content">
            <div class="login-page">
                <div class="login-form">

                    <h5 class="title is-5">
                        {{ title }}
                        <i class="material-icons">
                            info_outline
                        </i>
                    </h5>

                
                    <div class="field">
                        <input type="email" v-model="email" class="input" placeholder="Email">
                    
                    </div>

                    <div class="field">
                        <input type="password" class="input" v-model="password" placeholder="Пароль">
                    </div>

                    <div class="field">
                        <button class="button is-success" @click="login()">
                            Войти
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>
</template>
<script>

export default {
    data() {
        return {
            title:"Basis",
            email:"",
            password:"",
            token:""
        }
    },
    
    emits:['login'],

    methods: {
        login(){
            axios.get('sanctum/csrf-cookie').then(response => {

                axios({
                    url: route("postLogin"),
                    method:"post",
                    data:{
                        email : this.email,
                        password : this.password
                    }, 
                    headers: {
                        accept: 'application/json',
                    }
                }).then(response => {

                    let data = response.data;

                    if (data.result) { 
                        this.$toast.success(data.messages);
                        this.$emit("login")
                        return;
                    }
                    this.$toast.error(data.errors);
                })
            });
        }
    },
}
</script>