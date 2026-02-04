<template>
    <div>
        
        <h1 class="title-1 has-text-centered">
            Предлагаемые изменения для статей
        </h1>
        <Tabs value="0">
            <TabList>
                <Tab value="0">Предложения</Tab>
                <Tab value="1" v-if="!forAdmin">Мои предложения</Tab>
                <Tab value="2">Отклоненные</Tab>
            </TabList>
            <TabPanels>
                <TabPanel value="0">
                    <p class="m-0">
                        <elements-suggeted
                            :suggested-changes="suggestedChanges"
                            :edit="false"
                            @suggestion-apply="apply"
                            @suggestion-reject="reject"
                            ref="suggestionsList"
                        ></elements-suggeted>
                    </p>
                </TabPanel>
                <TabPanel value="1">
                    <p class="m-0">
                        <elements-suggeted
                            :suggested-changes="suggestedChanges"
                            :edit="true"
                        ></elements-suggeted>
                    </p>
                </TabPanel>
                <TabPanel value="2">
                    <p class="m-0">
                        <elements-suggeted
                            :suggested-changes="rejected"
                            :edit="false"
                        ></elements-suggeted>
                    </p>
                </TabPanel>
            </TabPanels>
        </Tabs>
    </div>
</template>
<script>

import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import TabPanels from 'primevue/tabpanels';
import TabPanel from 'primevue/tabpanel';
import ElementsSuggeted from '../modules/elements-suggeted.vue';
import axios from 'axios';
import { nextTick } from 'vue';

export default {
    
    components:{
        Tabs,
        TabList,
        Tab,
        TabPanels,
        TabPanel,
        ElementsSuggeted
    },

    data() {
        return {
            /**
             * массив с данными предложений
             */
            suggestedChanges: [],

            /**
             * отклоненные предложения
             */
            rejected:[],

            forAdmin: false
        }
    },
    async created() {
        let url = '';
        if (this.$route.matched[0]['name'] == 'settings') {
            url = route("settings.suggested-changes-for-admin");
            this.forAdmin = true;
        }else{
            url = route("objects.suggested-changes-for-author");
        }
        
        let response = await axios.get(url).catch(error => {
                    console.log(error)
                });

        this.suggestedChanges = response.data.suggested;

        this.rejected = response.data.rejected;

        if (this.$route.hash) {
            let id = this.$route.hash.split("#")[1];

            this.suggestedChanges.forEach((suggestion) => {
                    
                if (suggestion.id == id) {
                    
                    suggestion.open = true;
                    nextTick(()=>{
                        this.$refs.suggestionsList.scrollTo(suggestion.id)
                    })
                
                    return;
                }
            })
        }
        
    },

    methods: {
        /**
         * 
         * @param suggestion 
         */
        async apply(suggestion){

            let response = await axios.post(
                route("objects.note", suggestion.object_id),
                suggestion
            ).catch(error => {
                
                // // обработка ошибки
                this.$router.push("/"+error.status);
                if (error.response) {
                    this.$toast.error(error.response.data.message);
                    
                }
                // console.clear()
            });
            if(response.data.error){
                this.$toast.error(response.data.error);
                return;
            }

            axios.get(route("suggestion-force-delete", suggestion.id))

            this.$toast.success("Сохранено");
            this.$router.push("/objects/note/"+response.data.note.id)
        },

        /**
         * 
         * @param suggestion 
         */
        reject(suggestion){
            axios.get(route("reject-suggestion", suggestion.id),).then(response => {
                let i = this.suggestedChanges.findIndex(el => {
                    return el.id == suggestion.id
                });

                this.suggestedChanges.splice(i, 1);

                suggestion.open = false;

                this.rejected.push(suggestion);
            }).catch(error => {
                // обработка ошибки
                this.$router.push("/"+error.status);
                if (error.response) {
                    this.$toast.error(error.response.data.message);
                    
                }
            })
        }
    },
}
</script>