<template>
    <div class="modal is-active">
        <div class="modal-background"  @click="close()"></div>
        <div class="modal-card">

            <header class="modal-card-head">
                <p class="modal-card-title">
                    Обратная связь
                </p>
            </header>

            <section class="modal-card-body">
                <div class="field" >
                    <label class="label">
                        Тема обращения
                    </label>

                    <input type="text" v-model="feedback.title" class="input">
                </div>
                <div class="field" >
                    <label class="label">
                        Обращение
                    </label>

                    <md-editor-component
                        :text="feedback.text"
                        @update:text="updateText"
                        :default-show-toc="false"
                        :edit="true"
                    ></md-editor-component>
                </div>
            </section>

            <footer class="modal-card-foot">
                <div class="buttons">
                    <button class="button is-success" @click="save()">Сохранить</button>
                    <button class="button" @click="close()">Закрыть</button>
                </div>
            </footer>
        </div>
    </div>
</template>
<script>
export default {

    data() {
        return {
            feedback: {}
        }
    },
    
    emits:["close-modal"],

    methods: {
        save(){
            axios({
                url: route("feedback-save"),
                method:"post",
                data: this.feedback
            }).then(response => {
                if (response.data.result) {
                    this.$toast.success("Отправлено");
                    this.$emit("close-modal");
                    return;
                }

                this.$toast.error(response.data.error)
            }).catch(error => {
                 
                
                // // обработка ошибки
                if (error.status != 422) {
                    this.$router.push("/"+error.status);
                    return;
                }
                
                if (error.response) {
                    this.$toast.error(error.response.data.message);
                    
                }
            })
        },

        updateText(text){
            this.feedback.text = text;
        },

        /**
         * 
         */
        close(){

            this.feedback = {};
            this.$emit("close-modal");
        }
    },
}
</script>