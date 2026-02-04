<!-- чтение/редактирование для расширений загружаемых файлов -->
 <template>
    <div>
        <div class="modal is-active">
                <div class="modal-background" @click="$emit('cancel')"></div>
                <div class="modal-content">
                    <header class="modal-card-head">
                        <p class="modal-card-title">Подтверждение сохранения</p>
                    </header>
                    <section class="modal-card-body">

                        <div class="block" v-if="change.length > 0">
                            <label class="label">Измененные расширения</label>
                            <div v-for="element in change">

                                <Checkbox v-model="dataBeingSent['change']" :inputId="element.id"  :value="element" /> 
                                ID:{{ element.id }} 

                                <div v-if="element.update_type">
                                    Тип: {{ labels[ copyOriginal[element.id]['type']] }} -> {{ labels[element.type] }}
                                </div>
                                <div v-else>
                                    Тип: {{ labels[element.type] }}
                                </div>

                                <div v-if="element.expansion != undefined">

                                    Расширение: {{ copyOriginal[element.id]['expansion'] }} -> {{ element.expansion ? element.expansion : 'Заполните Расширение!' }} 
                                
                                </div>
                                <div v-if="element.upload">
                                    Загрузка: {{ copyOriginal[element.id]['upload'] ? "Разрешено" : "Запрещено" }} -> {{ element.upload ? "Разрешено" : "Запрещено"}}
                                </div>
                            </div>
                        </div>
                        <div class="block" v-if="newExpansions.length > 0">
                            <label class="label">Новые расширения</label>
                                <div v-for="(expansion, index) in newExpansions" :key="index">
                                    
                                    <Checkbox v-model="dataBeingSent['new']" :inputId="expansion.id"  :value="expansion" /> 
                                    New#{{ index }}
                                    <div>
                                        Тип: {{ expansion.type ? labels[expansion.type] : 'Заполните Тип!' }}
                                    </div>
                                    
                                    <br>
                                    <div>
                                        Расширение:  {{ expansion.expansion ? expansion.expansion : 'Заполните Расширение!' }} 
                                    </div>
                                    <div>
                                        Загрузка: {{ expansion.upload ? "Разрешено" : "Запрещено"}} 
                                    </div>
                                </div>

                        </div>
                    </section>
                    <footer class="modal-card-foot">
                        <div class="buttons">
                            <button class="button is-success" @click="$emit('save-expanseions')">Сохранить изменения </button>
                            <button class="button"  @click="$emit('cancel')">Cancel</button>
                        </div>
                    </footer>
                </div>
                <button class="modal-close is-large" aria-label="close" @click="$emit('cancel')"></button>
            </div>
    </div>
 </template>
 <script>
 import Checkbox from 'primevue/checkbox';

 export default {
    components:{ Checkbox },
    props:{
        dataBeingSent:{
            type:Array,
            required:true,
            default:Array
        },
        change:{
            type:Array,
            required:false,
            default:Array
        }, 
        newExpansions:{
            type:Array,
            required:false,
            default:Array
        },
       
        labels:{
            type: Object,
            required: true
        },

        copyOriginal:{
            type: Array,
            required: true
        }
    },

    emits:["save-expanseions", "cancel", ],
   
 }
 </script>