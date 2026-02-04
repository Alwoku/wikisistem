<template>
    <div>
        
        <p class="menu-label">Избранное</p>

        <ul class="menu-list" v-if="loading">
            <li v-for="object in objects">
                    
                <router-link :to="object.route" v-if="object.type == 'note'">

                    {{ object.label }}

                </router-link>

                <div class="is-flex is-justify-content-space-between is-align-items-center" v-else>
                    <a :class="{'is-active':object.open}"
                        @click="open(object)">
                            {{ object.label }}
                    </a>
                    <router-link :to="object.route" 
                        class="button button-route-folder-in-menu">
                        <span class="material-icons">
                            play_arrow
                        </span>
                    </router-link>

                </div>
                <ul v-if="object.children && object.open">
                    <li v-for="child in object.children">
                        <child-object-element
                            :object="child"
                        ></child-object-element>
                    </li>
                </ul>
            </li>
        </ul>
    </div>  
</template>
<script>
import ChildObjectElement from './child-object-element.vue';
import { useToggleFavoritesStore } from '../../store.js';

export default {
    
    components:{ChildObjectElement},

    data() {
        return {
            objects: [],

            loading: false,

            store: useToggleFavoritesStore(),
        }
    },
    created() {
        this.load();
        setInterval(() => {
            this.load();
        }, 20000)
        
    },
   
    watch:{
        'store.update': function (val){
            if (val) {
                this.load();  
                this.store.updated();
            }   
        }
    },

    methods: {
        
        async load(){
            let response = await axios.get(route("objects.user-favorites"));

            this.objects = response.data.objects;
            
            this.loading = true;
            
            
        },

        open(object){

            if (object === null || object.type === 'note') {
                return;
            }

            object.open = !object.open;
            
            //  фиксация открытых папок 

            axios.post(route("objects.save-condition-folders", object.id), { condition: object.open});
        }
    },
}
</script>