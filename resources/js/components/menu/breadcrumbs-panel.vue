<template>
    <div class="is-flex is-justify-content-space-between">

        <button v-if="object && object.id"  
                @click="$emit('reload-object')" 
                class="button m-1" 
                title="Обновление данных списка групп">

            <span class="material-icons">
                refresh
            </span>
        </button>
        <div class="block-breadcrumbs" >
            <Breadcrumb :home="home" :model="breadcrumbs" v-if="object">
                <template #item="{ item }">
                    <router-link class="cursor-pointer" :to="item.route">
                        {{ item.label }}
                    </router-link>
                </template>
                <template #separator> / </template>
            </Breadcrumb>
        </div>
        <div class="is-flex is-align-items-flex-start">
            <button  v-if="object && object.id" class="p-2" @click="favorites()">
                <span class="material-icons has-text-success" v-if="object.favorites">
                    bookmark
                </span>
                <span class="material-icons" v-else>
                    bookmark_border
                </span>
            </button>
            <button v-if="object && object.id" 
                @click="$emit('toggle-edit', object)" 
                class="p-2">
                <span class="material-icons" v-if="edit">
                    text_snippet
                </span>

                <span class="material-icons" v-else>
                    edit
                </span>
            </button>
            <button v-if="object && object.id" 
                class="p-2">

                <span class="material-icons has-text-danger" 
                        title="Удалить"
                        @click="$emit('delete-object')">
                    delete
                </span>
            </button>
            
            <div>
                <div class="dropdown is-hoverable p-2">
                    <div class="dropdown-trigger">
                        <div class="is-flex">
                            <span class="material-icons">
                                more_vert
                            </span>
                            <!-- счетсчик предложенных изменений -->
                            <sup class="has-text-danger" 
                                v-if="object && object.type === 'note' 
                                     && object.suggested_changes.length > 0">
                                +{{ object.suggested_changes.length }}
                            </sup>
                        </div>
                    </div>
                    <div class="dropdown-menu block-menu-for-objects">
                        <div class="dropdown-content">
                            <div class="dropdown-item">
                                
                                <router-link to="/objects/note">
                                    Создать статью
                                </router-link>
                            
                            </div>
                    
                            <div class="dropdown-item"> 
                                <router-link  to="/objects/folder">
                                    Создать папку
                                </router-link> 
                            
                            </div>
                            <div v-if="object">

                                <div class="dropdown-item" v-if="object.type === 'note'">
                                    <router-link :to="'/objects/versions/'+object.id">
                                        Простомотреть историю
                                    </router-link>
                                </div>
                                <div class="dropdown-item" 
                                    v-if="object.type === 'note'"
                                    @click="$emit('open-suggested-changes')">
                                    <a>
                                        Предложить изменения 
                                    </a>
                                </div>
                                <div class="dropdown-item" 
                                    @click="$emit('open-list-suggested-changes')"
                                    v-if="object.type === 'note' && object.suggested_changes.length > 0">
                                    <a>
                                        Просмотр предложений изменений 
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
import  Breadcrumb  from 'primevue/breadcrumb';
import { useToggleFavoritesStore } from '../../store.js';

export default {
    components:{
        Breadcrumb,
        
    },
    props:{
        object:{
            type:Object,
            required: false
        },
        edit:{
            type:Boolean,
            required:false
        }
    },

    data() {
        return {
            home:{ 
                label: 'Главная',
                route: '/'
             },
            breadcrumbs:[],
            store: null,
        }
    },

    emits:[
        "toggle-edit", 
        "delete-object", 
        "reload-object", 
        "open-suggested-changes",
        "open-list-suggested-changes"
    
    ],
    created() {
        // если параметр объекта не был передан, то крошки не загружаются
        if (this.object) {
            this.updateBreadcrumbs()
        }

        this.store = useToggleFavoritesStore();
    },

    methods: {
        /**
         * обновление хлебных крошек
         */
        updateBreadcrumbs(){
            axios.get(route("objects.breadcrumbs",this.object.id)).then(response =>{
                this.breadcrumbs = response.data;
            })
        },

        /**
         * добавляет/убирает объект в избранное
         */
        favorites(){
            

            axios.get(route("objects.toggle-favorites", this.object.id)).then(response =>{
                if (response.data.error) {
                    this.$toast.error(response.data.error);
                    return;
                }

                this.$toast.success(response.data.message);
                this.object.favorites = response.data.favorites;

                
                this.store.launchUpdate();

            }).catch(error =>{

                // обработка ошибки
                this.$router.push("/"+error.status);

                console.clear()
            })
        },
        open(){
            console.log("ok");
            this.windowChangesObject = true;
            
        }
    },
}
</script>
<style lang="scss">
    .block-breadcrumbs{
        width: 80%;
        display: flex;
        align-items: center;
    }
    .dropdown-menu.block-menu-for-objects {
        left: auto !important;
        right: 0 !important;
    }
    .p-breadcrumb {
        background: transparent !important; 
        padding: 0 !important;
    }
    li.p-breadcrumb-item{
        list-style: none !important;
        margin: 0 !important;
    }
</style>