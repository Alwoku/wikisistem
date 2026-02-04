<template>
    <div>
        <div class="block">
            
            <div class="has-text-centered has-block-sticky-top">
                <div class="control has-icons-right">
                    <input class="input"  
                        placeholder="Поиск" 
                        autofocus
                        v-model="search"
                        @keyup="globalFinder"/>
                    <span class="icon is-right">
                        <span class="material-icons">
                            search
                        </span>
                    </span>
                </div>
            </div>
        </div>
        <!-- todo добавить стили -->
        <section class="hero">
            <div class="hero-body">
                <div v-for="object in foundObjects" class="block">
                    
                    <a  @click="openModalObject(object)">
                        
                        <h4 class="title is-4 is-flex">
                            <span class="material-icons" v-if="object.type == 'note'">
                                article
                            </span>
                            <span class="material-icons" v-else>
                                folder
                            </span>
                            <span class="ml-1">
                                {{ object.name }}
                            </span>
                            
                        </h4>
                        <div class="subtitle is-6  ml-3 " v-html="object.search" ></div>
                    </a>
                </div>
            </div>
        </section>
        <window-preview-objects
            v-if="activeModale"
            :object="modalObject"
            @close-modal="closeModelObject"
        ></window-preview-objects>

    </div>
</template>
<script>
import WindowPreviewObjects from '../windows/window-preview-objects.vue';
    
export default {

    components:{WindowPreviewObjects},
    
    data() {
        return {
            /**
             * строка поиска
             */
            search:"",
            
            /**
             * найденные объекты
             */
            foundObjects: [],
            
            /**
             * объект для предпросмотра в модальном окне
             */
            modalObject:{},
            
            /**
             * отображение модального окна
             */
            activeModale: false,
        }
    },
    
    created() {
        this.search = this.$route.params.search;
        this.globalFinder();
    },

    methods: {
         /***
         * поиск по объектам
         */
        async globalFinder(){
            
            if(this.search.length < 2){
                if(this.foundObjects.length > 0) this.foundObjects = [];
                return;
            }

            let response = await axios.post(route("objects.global-finder"), { search: this.search})
            
            this.foundObjects = response.data;
        },

        /**
         * открытие модального окна просмотра объекта
         * @param object 
         */
        async openModalObject(object){
            
            
            let response = await axios.get(route("objects."+object.type, object.id)).catch(error =>{
                    this.$toast.error("Ошибка запроса");
                    // console.clear()
                });
            this.modalObject = response.data[object.type];
            
            this.activeModale = true;
        },

        /**
         * закрытие модального окна просмотра объекта
         */
        closeModelObject(){
            this.activeModale = false;
            this.modalObject = {};
        }
    },
}
</script>