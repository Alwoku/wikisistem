<template>
    <div>
        <aside class="menu">
            <ul class="menu-list">
                <li v-for="(suggestion, index) in suggestedChanges">
                    <a :class="{'is-active': suggestion.open}"
                        @click="suggestion.open = !suggestion.open"
                        :ref="suggestion.id">
                        {{ suggestion.name }} 
                        
                    </a>
                    <ul class="menu-list" v-if="suggestion.open">
                        <li>
                            <div class="block">
                                Автор: {{ suggestion.user.name }}
                            </div>
                            <div class="block p-1">
                                <label class="label" v-if="suggestion.parent_id == suggestion.object.parent_id">
                                    Родительская папка: {{ suggestion.parent_object ? suggestion.parent_object.name : 'N/a' }}
                                </label>
                                <label class="label" v-else>
                                    Предложение сменить родительскую папку:  
                                    {{ suggestion.object.parent ? suggestion.object.parent.name : 'N/a' }} 
                                    →  
                                    {{ suggestion.parent_object ? suggestion.parent_object.name : 'N/a' }}
                                </label>
                            </div>

                            <div class="block p-1">
                                <label class="label" v-if="suggestion.name == suggestion.object.name">
                                    Название: {{ suggestion.name }}
                                </label>
                                <label class="label" v-else>
                                    Предложение сменить название:  {{ suggestion.name }} →  {{ suggestion.object.name }}
                                </label>
                            </div>
                            <div class="block">
                                <table class="table is-fullwidth">
                                    <thead>
                                        <tr>
                                            <th>
                                                Измененная версия 
                                            </th>
                                            <th>
                                                Актуальная версия
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                                <Diff
                                    language="markdown"
                                    :prev="suggestion.text"
                                    :current="suggestion.object.content.content"
                                />
                            </div>
                            <div class="block">
                                <label class="label">
                                    Комментарий
                                </label>

                                <div>{{ suggestion.comment }}</div>
                            </div>

                            <div class="is-flex buttons-create-group">
                                <button class="button is-success m-1" @click="$emit('suggestion-apply', suggestion)">
                                    Применить
                                </button>
                                <button class="button is-danger m-1" @click="$emit('suggestion-reject', suggestion)">
                                    Отклонить
                                </button>
                                <button v-if="edit" class="button" @click="suggestion.edit = !suggestion.edit">
                                    Редактировать (будет позже)
                                </button>
                            </div>
                        </li>
                        
                    </ul>
                    
                </li>
            </ul>
        </aside>
    </div>
</template>
<script>
export default {
    props:{
        suggestedChanges:{
            type:Array,
            required:true
        },
        edit:{
            type:Boolean,
            required:true,
        }
    },

    emits:["suggestion-reject", "suggestion-apply"],

    methods: {
        
        scrollTo(id){
            
            this.$refs[id][0].scrollIntoView({alignToTop:true})
        },  
        

    },

}
</script>