<!-- создание запрета/разрешение на загрузку на сайт расширений файлов -->
<template>
    <div>
        <div class="is-flex is-justify-content-space-between has-block-sticky-top">

            <button @click="load(true)"
                    class="button m-1" 
                    title="Обновление данных списка групп">

                <span class="material-icons">
                    refresh
                </span>
            </button>

            <div class="is-flex">

                <button @click="toggleEditable()" 
                    class="p-2">
                    <span class="material-icons" v-if="editable">
                        text_snippet
                    </span>

                    <span class="material-icons" v-else>
                        edit
                    </span>
                </button>

                <button title="Создать новую группу"
                    @click="createExpansions()">
                    <span class="material-icons has-text-success">
                        add
                    </span>
                </button>

            </div>
        </div>
        <div class="block">
            <table class="table is-fullwidth">
                <thead v-if="!created">
                    <tr>
                        <th>
                            ID
                        </th>
                        <th>
                            Тип
                        </th>
                        <th>
                            Расширение
                            <span title="Внимание! Расширения пишутся без точки впереди. Например png " 
                                class="material-icons has-text-success">
                                info
                            </span>
                        </th>
                        <th>
                            Загрузка
                        </th>
                        <th>
                            <div>
                                <button class="button is-danger" v-if="!selecteToDelete" @click="selecteToDelete = !selecteToDelete">
                                    
                                        Удалить несколько
                                    
                                </button>
                                <button class="button" v-else @click="cancelMassDelete()">
                                        Отмена
                                </button>
                                
                            </div>

                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(expansion, index) in expansions" :key="index" >
                        <td>
                            {{ expansion.id }}
                        </td>
                        <td>
                                <v-select
                                    :reduce="reduce"
                                    :options="types"
                                    v-model="expansion.type"
                                    :disable="!editable"
                                    @change="changeExpansion(expansion, 'type')"
                                ></v-select>
                        </td>
                        <td>
                            <div v-if="editable">
                                <input type="text" 
                                    @keyup="changeExpansion(expansion, 'expansion')"
                                    v-model="expansion.expansion" 
                                    class="input"
                                    @focus="saveOrigin(expansion)"
                                    placeholder="Введите расширение без точки впереди. Например: zip">
                            </div>
                            <div v-else>
                                {{ expansion.expansion }}
                            </div>
                        </td>
                        <td>
                            <div v-if="editable" class="select">
                                <select v-model="expansion.upload"
                                    @change="changeExpansion(expansion, 'upload')"
                                    @focus="saveOrigin(expansion)">
                                    <option :value="1">Разрешено</option>
                                    <option :value="0">Запрещено</option>
                                </select>
                            </div>
                            <div v-else>
                                {{ expansion.upload ? "Разрешено" : "Запрещено" }}
                            </div>
                        </td>
                        <td>
                            
                            <div v-if="selecteToDelete">

                                <Checkbox v-model="selected" :inputId="expansion.id"  :value="expansion" />
                            
                            </div>
                            <div v-else>
                                <button class="button is-outlined is-danger" @click="deleteExpansion(expansion)">
                                    <span class="material-icons">
                                        delete
                                    </span>
                                </button>
                            </div>

                        </td>
                    </tr>
                    <tr ref="newExpansionBlock"
                        v-for="(expansion, index) in newExpansions" :key="index" >
                        <td>
                            New
                        </td>
                        <td>
                                <v-select
                                    :reduce="reduce"
                                    :options="types"
                                    v-model="expansion.type"
                                ></v-select>
                        </td>
                        <td>
                                <input type="text" 
                                    v-model="expansion.expansion" 
                                    class="input"
                                    placeholder="Введите расширение без точки впереди. Например: zip">
                        </td>
                        <td>
                            <div class="select">
                                <select v-model="expansion.upload">
                                    <option :value="1">Разрешено</option>
                                    <option :value="0">Запрещено</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            
                            <div v-if="selecteToDelete">

                                <Checkbox v-model="selected" :inputId="expansion.id"  :value="expansion" />
                            
                            </div>
                            <div v-else>
                                <button class="button is-outlined is-danger" @click="deleteNewExpansion(index)">
                                    <span class="material-icons">
                                        delete
                                    </span>
                                </button>
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
           
        </div>
        <div class="block stiky-block-buttons">
            
            <button class="button is-success m-1" 
                @click="checkedSave()"
                v-if="edit()">
                Сохранить
            </button>
            <button class="button is-danger m-1" v-if="edit()"  @click="cancel()">
                Отмена
            </button>

            <button class="button is-danger m-1" v-if="selecteToDelete"  @click="deleteExpansions()">
                Удалить 
            </button>
        </div>  
        
        <div v-if="confirmSave">
            <window-save-expansions
                :data-being-sent="dataBeingSent"
                :change="change"
                :new-expansions="newExpansions"
                :labels="labels"
                :copy-original="copyOriginal"
                @save-expanseions="save"
                @cancel="confirmSave = false"
            ></window-save-expansions>
        </div>
    </div>
</template>
<script>

import { nextTick } from 'vue'
import WindowSaveExpansions from '../windows/window-save-expansions.vue';
import Checkbox  from 'primevue/checkbox';


export default {
    components:{
        WindowSaveExpansions,
        Checkbox
    },
    data() {
        return {
            
            expansions: [],

            labels: [],
            /**
             * режим редактирования
             */
            editable: false,
            
            /**
             * массив новых расширений
             */
            newExpansions:[],
            /**
             * фиксация изменений в уже созданных расширениях
             */
            change:[],

            /**
             * окно с подтверждением сохранения
             */
            confirmSave: false,

            /**
             * копии оригинальных расширений
             */
            copyOriginal: {},

            /**
             * данные для отправки на бекенд
             */
            dataBeingSent: {},

            /**
             * типы загружаемых файлов
             */
            types: [],

            /**
             * режим массового удаления
             */
            selecteToDelete: false,

            selected: [],
        }
    },

    
    created() {
        // загрузка данных для списков расширений 
        this.load()

        
    },

    methods: {

        async load(reload = false){
            let response = await axios.get(route("settings.expansion-files")).catch(error => {

                this.$router.push("/"+error.status);

                this.$toast.error(error.message);
                console.clear()
            })
            let data = response.data;
            
            this.expansions = data.expansions ;
            this.labels = data.labels;
            this.types = data.types

            if (reload) {
                this.$toast.success("Обновлено");
            }
        },
        /**
         * переключение режима редактирования 
         * @param type 
         */
        toggleEditable(){

            this.editable = !this.editable;
        
        },

       
        /**
         * создание нового расширения
         */
        createExpansions(){
            this.newExpansions.push({
                upload:0,
                expansion:"",
                temporaryId: Math.floor(Math.random() * 1000000001)
            });

            nextTick(()=>{
                if (this.newExpansions.length == 1) {
                    
                    this.$refs.newExpansionBlock[0].scrollIntoView({ behavior: "smooth" })
                }
            })
        },
        /**
         * проверка на режим редатирования 
         * @param type 
         */
        edit(){
            return this.editable || this.newExpansions.length > 0;
        },

        /**
         * Отмена всех действий 
         */
        cancel(){

            if (!window.confirm("Действие удалит все изменения во всех расширениях. Вы уверены?")) {
                return;
            }

            this.editable = false;
            this.load();
            this.change = [];
            this.newExpansions = [];
            this.copyOriginal = {}
            this.confirmSave = false;
        },

        save(){

            axios.post(
                route("settings.save-expansion-files"),
                this.dataBeingSent).then(response => {

                    let data = response.data;
                    

                    if (data.errors) {

                        let newExp = data.new;

                        for(let key in newExp){
                            
                            let i = this.newExpansions.findIndex(el => {
                                return el.temporaryId == newExp[key]['temporaryId']
                            });

                            this.newExpansions.splice(i, 1);

                             
                            let index = this.dataBeingSent.new.findIndex(el => {
                                return el.temporaryId == newExp[key]['temporaryId']
                            });

                            this.dataBeingSent.new.splice(index, 1)


                            delete newExp[key]['temporaryId']

                            this.expansions.push(newExp[key])


                            this.$toast.success("Сохраненo Расширение:"+newExp[key]['expansion']);
                        }

                        let saved = data.change;


                        for(let key in saved){
                               
                            
                            let i = this.change.findIndex(el => {
                                return el.id == saved[key]['id']
                            });

                            this.change.splice(i, 1);

                            let index = this.dataBeingSent.change.findIndex(el => {
                                return el.temporaryId == newExp[key]['temporaryId']
                            });

                            this.dataBeingSent.change.splice(index, 1)

                            this.$toast.success("Сохраненo Расширение:"+saved[key]['expansion']);
                        }


                        let errors = data.errors;

                        for (var key in errors) {
                            this.$toast.error(errors[key]);
                        }
                        
                        return;
                    }

                    for(let key in data.new){
                        this.expansions.push(data.new[key])
                    }

                    this.$toast.success("Сохраненo ");


                    this.newExpansions = [];
                    this.change = [];
                    this.copyOriginal = {};
                    this.editable = false;
                    this.confirmSave = false
                    
            }).catch(error => {

                if (error.status == 422 ) {
                
                    if (error.response) {
                        let data = error.response.data;

                        var errors = data.errors;
                        
                        for (var key in errors) {
                            this.$toast.error(errors[key]);
                        }

                        console.clear();
                        return;
                    }

                    this.$toast.error("Ошибка при сохранении записи");

                }
                

                // если прав доступа не достаточно 
                // делаем редирект 
                // this.$router.push("/"+error.status);
                return;
            })
        },
        
        /**
         * сопирование оригинальных данных
         */
        saveOrigin(expansion){
            if (expansion.id && !this.copyOriginal[expansion.id]) {
                
                this.copyOriginal[expansion.id] = JSON.parse(JSON.stringify(expansion));
                
            }
        },

        /**
         * 
         */
        checkedSave(){
            this.dataBeingSent['change'] = this.change.length > 0 ? this.change : [];

            this.dataBeingSent['new'] = this.newExpansions.length > 0 ? this.newExpansions : [];

            this.confirmSave = true;
        },


        deleteExpansion(expansion){
            this.selected = [expansion];
            this.deleteExpansions()
        },

        deleteExpansions(){
            // 
            if (!this.created) {
                axios.post(route("settings.delete-expansions-files"), this.selected).then(response => {

                    if (response.data.errors) {
                        let errors = response.data.errors;
                        for(let key in errors){
                            this.$toast.error(errors[key]);
                        }

                        return;
                    }

                    for(let key in this.selected){
                        
                        let index = this.expansions.findIndex(el => {
                            
                            return el.id == this.selected[key].id;
                        });

                        this.expansions.splice(index, 1);
                    }

                    this.$toast.success(response.data.message);

                    this.selecteToDelete = false;
                    
                }).catch(error => {

                    this.$router.push("/"+error.status);

                    this.$toast.error(error.message);
                    console.clear()
                })
            }

            // this.expansions.splice(i, 1);
        },

        /**
         * отслеживание изменений
         * @param expansion 
         */
        changeExpansion(expansion, key){

            if (!expansion.id) {
                return;
            }

            
            let i = this.change.findIndex(el => {
                return el.id == expansion.id;
            });

            
            if (i >= 0) {
                this.change[i][key] = expansion[key];
                return;
            }

            let data = {
                id: expansion.id,
                type: expansion.type
            }
            
            if(expansion.type != this.copyOriginal[expansion.id]['type']){
                data.update_type = true;
            }

            data [key] = expansion[key];
            
            this.change.push(data);
            
        },

        reduce(options){
            return options.id
        },

        cancelMassDelete(){
            this.selected = [];
            this.selecteToDelete = false;
        },

        deleteNewExpansion(index){
            this.newExpansions.splice(index, 1)
        }
    },
}
</script>