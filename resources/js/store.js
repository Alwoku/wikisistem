import { defineStore } from 'pinia'

export const useToggleFavoritesStore = defineStore('ToggleFavorites', {
    state: () => ({ update: false }),
    actions: {
        launchUpdate(){
            this.update = true;
        },

        updated(){
            this.update = false;
        }
    }
});