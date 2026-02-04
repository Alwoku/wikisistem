<template>
    <div>
        
        <div class="is-flex is-justify-content-space-between pt-1 has-block-sticky-top">
            <button @click="load(true)" title="Обновление данных списка групп">
                <span class="material-icons">
                    refresh
                </span>
            </button>

        </div>

        <h1 class="title  has-text-centered">Обратная связь</h1>



        <div class="block">
            <aside class="menu">
                <ul class="menu-list">
                    <li v-for="(feedback, index) in feedbacks">
                        <a :class="{'is-active': feedback.open}"
                            @click="feedback.open = !feedback.open">
                            {{ feedback.title  }}
                            от
                            {{ feedback.created_at }}
                        </a>
                        <ul class="menu-list" v-if="feedback.open">
                            <li>
                                <div class="block">
                                    
                                    <div class="field">
                                        <label class="label">
                                            Пользователь: {{ feedback.user.name }}
                                        </label>
                                    </div>

                                    <div class="field">
                                        <label class="label">
                                            Обращение
                                        </label>


                                        <md-editor-component
                                            :text="feedback.text"
                                            :default-show-toc="false"
                                            :edit="false"
                                        ></md-editor-component>
                                    </div>

                                    <div class="is-flex buttons-create-group">
                                        <button class="button is-success" 
                                            @click="closed(feedback, index)">
                                            Прочитано
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </aside>
        </div>

    </div>
</template>
<script>
export default {
    data() {
        return {
            /**
             * данные обратной связи
             * от пользователей
             */
            feedbacks:[]
        }
    },

    created() {
        this.load()
    },

    methods: {
        /**
         * 
         * @param refresh 
         */
        async load(refresh = false){

            let response = await axios.get(route("settings.feedback")).catch( error => {

                    // // обработка ошибки
                    this.$router.push("/"+error.status);
                    if (error.response) {
                        this.$toast.error(error.response.data.message);
                        
                    }
                });

            this.feedbacks = response.data.feedbacks;
        },

        /**
         * 
         */
        closed(feedback, i){
            axios.get(route("settings.feedback-delete", feedback.id)).then(response => {

                this.feedbacks.splice(i, 1);

                this.$toast.success("Прочитано");
            }).catch(error => {

                // // обработка ошибки
                this.$router.push("/"+error.status);
            });

            
        },

    }
}
</script>