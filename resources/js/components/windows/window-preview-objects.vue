<template>
    <div class="modal is-active">
        <div class="modal-background" @click="$emit('close-modal')"></div>
        <div class="modal-card">
            <header class="modal-card-head">
                <span class="mr-2">
                    {{ object.name }}
                </span>
                 
                <span v-if="object.type == 'note'">
                    Ver.{{ object.version }}
                </span>
            </header>
            
            <section class="modal-card-body">
                <!-- todo breadcrumbs -->
                <!-- <div class="block">
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
                </div> -->

                <div class="block">
                    Родительская папка:

                    <router-link v-if="object.parent_id != null" :to="'/objects/folder/'+object.parent_id">
                        {{ object.parent.name }}
                    </router-link>
                    <span v-else>
                        Нет
                    </span>
                </div>

                <div v-if="object.type == 'note'">
                     <md-editor-component
                        v-if="object.text"
                        :text="object.text"
                        :edit="false"
                    ></md-editor-component>
                    <span v-else>
                        Пусто   
                    </span>
                </div>
                <div v-else>
                    <div v-if="object.children.lenght != 0">
                        
                        <div class="field" v-for="child in object.children">
                            <label class="label">
                                <router-link :to="'/objects/'+child.type+'/'+child.id">
                                    {{ child.name }}
                                </router-link>
                                
                            </label>
                        </div>
                    </div>
                    <div v-else>
                        Эта папка пуста
                    </div>
                </div>
            </section>

            
            <footer class="modal-card-foot">
                <div class="buttons">
                    <router-link :to="'/objects/'+object.type+'/'+object.id" class="button is-success">
                        Перейти на страницу 
                    </router-link>
                    
                    <button class="button " @click="$emit('close-modal')">
                        Закрыть
                    </button>
                    
                </div>
            </footer>
        </div>
    </div>
</template>
<script>
export default {
    props:{
        /**
         * отображаемый объект 
         */
        object:{
            type: Object,
            required: true
        }
    },

    emits:["close-modal"],
    
}
</script>