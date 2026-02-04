<template>
    <div>
        <div class="left-panel-menu">
    
            <aside class="menu">
                <objects-menu v-if="openMenu === 'objects'"></objects-menu>
                <favorites-menu v-if="openMenu === 'favorites'"></favorites-menu>
                <settings-menu v-if="openMenu === 'settings' && user.is_admin"></settings-menu>
            </aside>
        </div>
    </div>
</template>
<script>
import FavoritesMenu from './favorites-menu.vue';
import objectsMenu from './objects-menu.vue';
import SettingsMenu from './settings-menu.vue';
export default {
    components: { 
        objectsMenu, 
        SettingsMenu,  
        FavoritesMenu
    },

    props:[ "openMenu", "user"],

    emits:[ "close-menu" ],

    // проверка переключения меню 
    watch:{
        openMenu: function(value){

            if (value === 'settings' &&
                !this.user.is_admin) {
                this.$emit("close-menu"); 
            }
            
        }
    }
}
</script>