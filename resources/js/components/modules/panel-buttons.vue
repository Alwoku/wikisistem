<!-- панель с кнопкиами внизу экрана -->
<template>
    <div class="panel-buttons has-text-centered">
        <div class="button-mobile-version" v-if="$isMobile()">
            <div class="dropdown is-up is-active">
                <div class="dropdown-trigger">
                    <button class="button is-success ">
                        <span class="material-icons">
                            pending
                        </span>
                    </button>
                </div>
                <div class="dropdown-menu" >
                    <div class="dropdown-content">
                        <div class="dropdown-item">
                            <buttons-notes-mobile></buttons-notes-mobile>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <buttons-notes></buttons-notes>
        </div>
        
    </div>
</template>
<script>
import buttonsNotesMobile from './buttons-notes-mobile.vue'
export default {
  components: { buttonsNotesMobile },
    
}
</script>
<style lang="scss">
@media (min-width: 1024px) {
    
    .panel-buttons{
        position: fixed;
        bottom: 0;
        width: 100%;
        left: 300px;
    }
}
@media (max-width: 1024px) {
    
    .panel-buttons{
        position: fixed;
        bottom: 0;
        width: auto;
        .button-mobile-version{
            right: 0;
            .dropdown-content {
                max-width: 60px;
                .dropdown-item {
                    padding: 5px !important;
                }
            }
        }
    }
}

</style>