<template>
   <div class="modal is-active">
        <div class="modal-background" @click="close()"></div>
        <div class="modal-card">

            <header class="modal-card-head">
                <p class="modal-card-title">{{ data.name }}</p>
                <button class="delete" aria-label="close"  @click="close()"></button>
            </header>


            <section class="modal-card-body">
              
                <div v-if="suggestion.created_at" class="field">
                    Предложение от {{ suggestion.created_at }}
                </div>

                <div class="field">
                    <v-select
                        :reduce="reduce"
                        :options="folders"
                        v-model="suggestion.parent_id"
                        placeholder="Выберите родительскую директорию"
                    ></v-select>
                </div>

                <div class="field">
                    <input
                        type="text"
                        class="input"
                        v-model="suggestion.name"
                        placeholder="Введите название">
                </div>    
                <div class="field">
                    <md-editor-component
                        :text="suggestion.text"
                        @update:text="updateText"
                        :default-show-toc="false"
                        :edit="true"
                    ></md-editor-component>
                </div>
                <div class="field">
                    <label class="label">
                        Комментарий к изменениям
                    </label>

                    <input type="text" class="input" v-model="suggestion.comment">
                </div>
            </section>

            <footer class="modal-card-foot">
                <div class="buttons">
                    <button class="button is-success" @click="saveSuggested()">Сохранить</button>
                    <button class="button" @click="close()">Отменить</button>
                </div>
            </footer>

        </div>
    </div>
</template>
<script>

export default {
    props:{
        data:{
            type:Object,
            required:true  
        },
        folders:{
            type:Array,
            required:true,
        }
    },

    data() {
        return {
             /**
             * копия статьи для последущего изменения
             */
            suggestion: {}, 

            origin: {},
        }
    },

    async created() {
        let response = await axios.get(route("objects.check-out-today-suggestion", this.data.id));

        if (response.data.suggestion) {
            this.suggestion = response.data.suggestion;
            this.$toast.success("Добавлено существующее предложение");
            this.origin =  JSON.parse(JSON.stringify(this.suggestion));

            return;
        }

        this.suggestion.object_id = this.data.id;
        this.suggestion.parent_id = this.data.parent_id;
        this.suggestion.name = this.data.name;
        this.suggestion.text = this.data.text;
        
        this.origin =  JSON.parse(JSON.stringify(this.suggestion));

    },

    emits:["close-window"],

    methods: {
        
        updateText(text){
            this.suggestion.text = text;
        },

        /**
         * закрытие окна
         */
        close(){

            // если изменений нет, то просто закрываем окно
            if(JSON.stringify(this.origin) === JSON.stringify(this.suggestion)){

                this.$emit("close-window");
                return;
            }

            if (!window.confirm("Есть не сохраненные изменения. Вы уверены?")) {
                return;
            }

            this.$emit("close-window");
        },

        reduce(options){
            return options.id
        },

        /**
         * сохранение предложения 
         */
        saveSuggested(){

            axios.post(
                route("objects.save-suggested-changes", this.data.id), 
                this.suggestion
            ).then(response => {
                this.$toast.success(response.data.message)
                this.$emit("close-window");
            }).catch(error => {
                console.log(error);
                
            })
        }
    },
}
</script>