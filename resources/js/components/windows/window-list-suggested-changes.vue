<template>
     <div class="modal is-active">
        <div class="modal-background"  @click="$emit('close-window')"></div>
        <div class="modal-card">

            <header class="modal-card-head">
                Предлагаемые изменения для {{ object.name }}
            </header>

            <section class="modal-card-body">
                <table class="table is-fullwidth">
                    <thead>
                        <tr>
                            <th>
                                Автор
                            </th>
                            <th>
                                Комментарий
                            </th>
                            <th>
                                Дата
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="suggestion in suggestedChanges" 
                            @click="redirect(suggestion)"
                            class="has-cursor-pointer">
                            <td>
                                {{ suggestion.user.name }}
                            </td>
                            <td>
                                {{ suggestion.comment }}
                            </td>
                            <td>
                                {{ suggestion.created_at }}
                            </td>
                        </tr>
                    </tbody>
                </table>

            </section>

            <footer class="modal-card-foot">
                <div class="buttons">
                    
                    <button class="button" @click="$emit('close-window')">Закрыть</button>
                </div>
            </footer>
        </div>
    </div>
</template>
<script>
export default {
    props:{
        /**
         * статья
         */
        object:{
            type: Object,
            required: true,
        },
        /**
         * предложенные изменений для данной статьи
         */
        suggestedChanges:{
            type: Array,
            required: true
        },

    },

    emits:["close-window"],

    methods: {
        
        redirect(suggestion){
            window.open("/suggested-changes-to-objects#"+suggestion.id, '_blank');
        }


    },
}
</script>