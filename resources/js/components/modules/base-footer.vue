<template>
    <div v-if="loaded">
        <div class="columns">
            <div class="column is-4">
                Система хранения знаний
                <br>
                © Айсвуд 2015–{{ footerData.date.year }}
                <br>
                <div class="note">
                    ver. {{ footerData.versionSite }} от {{ footerData.date.from }}
                    ({{ footerData.date.diffForHumans }}),
                    Laravel {{ footerData.versionLaravel }}, PHP {{ footerData.versionPhp }}
                    <br>
                    Информация на странице носит конфиденциальный характер
                </div>
            </div>
            <a class="column is-5" @click="openWindowInfo()">
                Информация о сайте
            </a>
            <a class="column is-5" @click="feedback = true">
                Обратная связь
            </a>
        </div>

        <div v-if="windowInfo && footerData.info">
            <window-history-of-changes
                :footer-data="footerData"
            ></window-history-of-changes>
        </div>

        <div v-if="feedback">
            <window-feedback
                @close-modal="feedback = false"
            ></window-feedback>
        </div>
    </div>
</template>
<script>
import WindowFeedback from '../windows/window-feedback.vue';
import WindowHistoryOfChanges from '../windows/window-history-of-changes.vue';

export default {

    components:{
        WindowHistoryOfChanges,
        WindowFeedback
    },

    props:{
        user:{
            type: Boolean,
            required: true
        },

        read:{
            type: Boolean,
            required:true
        }
    },

    data() {
        return {
            /**
             * открывает окно с актуальными данными 
             * о версии сайта
             */
            windowInfo: false,

            /**
             * 
             */
            footerData: [],

            loaded: false,

            feedback: false
        }
    },

    created() {
        
        this.load();
    },

    watch:{
        read: function (val){
            this.windowInfo = !val
            this.load(true);
        }
    },

    methods: {
        load(reload = false){
            axios.get(route("footer-info")).then(response => {

                if(reload) {
                    if (this.footerData.versionSite != response.data.versionSite ||
                        this.footerData.info.version != response.data.info.version) {
                        
                        this.footerData =  response.data;
                        this.footerData.reload = true;
                        this.$toast.error("Пожалуйста перезагрузите страницу");
                        return;
                    }
                }
                this.loaded = true;
                
                this.footerData =  response.data;
                this.windowInfo = !response.data.modalNotification
            })
        },

        openWindowInfo(){
            if (!this.footerData.info) {
                this.$toast.error("Информации об изменениях пока нет")
                return;
            }
            this.windowInfo = true;  
        },

        closeWindow(){
            axios.get(route("user-read-info"));
            this.windowInfo = false;
        }
    },
}
</script>