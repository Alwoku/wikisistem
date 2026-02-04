<template>
    <div>
        <aside class="menu">
            <p class="menu-label">Меню</p>

            <ul v-if="!objects || objects.length === 0 || !loading" class="menu-list">
                <li>
                    <router-link to="/">Главная</router-link>
                </li>
            </ul>
            <ul v-else class="menu-list">
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
        </aside>
    </div>
</template>
<script>
import ChildObjectElement from './child-object-element.vue'

export default {
    components:{ ChildObjectElement },
    data() {
        return {
            /**
             * дерево объектов
             */
            objects:null,
            loading: false
        }
    },
    mounted() {
       this.load()
       
        setInterval(() => {
            this.load()
        }, 15000)
    },
    methods: {
        
        /**
         * забирает дерево объектов
         */
        load(){
            axios.get(route("objects.free-objects")).then(response => {
                this.objects = response.data;
                this.loading = true
                
            })

        },
        open(object){

            // todo добавить фиксацию открытых папок 
            if (object === null || object.type === 'note') {
                return;
            }

            object.open = !object.open;

            axios.post(route("objects.save-condition-folders", object.id), { condition: object.open});
        }
    },
}
</script>
<style lang="scss">
    .button-route-folder-in-menu{
        max-width: 50px;
        padding: 0 !important;
    }
</style>