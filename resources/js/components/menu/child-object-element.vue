<template>
    <div>
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
        
    </div>
</template>
<script>
export default {
    props:{
        object:{
            type:Object,
            required:true
        }
    },

    methods: {
        open(){
            if (this.object.type === 'note') {
                return;
            }
            
            this.object.open = !this.object.open;

            axios.post(route("objects.save-condition-folders", this.object.id), 
                    { condition: this.object.open})
        }
    },
}
</script>